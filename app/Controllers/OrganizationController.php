<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OrganizationModel;
use Ramsey\Uuid\Uuid;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Exception;

class OrganizationController extends BaseController
{
    protected $organizationModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        $organizations = $this->organizationModel->orderBy('sort', 'ASC')->findAll();
        $data['organizations'] = $this->buildTree($organizations);
        $data['tree_json'] = json_encode($this->buildFancytreeData($organizations));

        // Calculate statistics
        $data['stats'] = $this->organizationModel->calculateStats($organizations);

        return view('backend/organizations/manage_organization', $data);
    }

    public function get_tree_data()
    {
        $organizations = $this->organizationModel->orderBy('sort', 'ASC')->findAll();
        return $this->response->setJSON($this->buildFancytreeData($organizations));
    }

    public function create()
    {
        $data = $this->request->getPost();

        $data['uuid'] = Uuid::uuid4()->toString();
        if (!empty($data['parend_id']) && $data['parend_id'] != 0) {
            $parent = $this->organizationModel->find($data['parend_id']);
            $data['level'] = $parent ? $parent['level'] + 1 : 1;
        } else {
            $data['level'] = 1;
            $data['parend_id'] = 0;
        }

        if (empty($data['sort'])) {
            $maxSort = $this->organizationModel->selectMax('sort')->first();
            $data['sort'] = $maxSort ? $maxSort['sort'] + 1 : 1;
        }

        $maxNode = $this->organizationModel->selectMax('node_id')->first();
        $data['node_id'] = $maxNode ? $maxNode['node_id'] + 1 : 1;

        if ($this->organizationModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'เพิ่มองค์กรสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถเพิ่มองค์กรได้']);
        }
    }

    public function update($id = null)
    {
        $data = $this->request->getPost();

        // Set level based on parent
        if (!empty($data['parend_id']) && $data['parend_id'] != 0) {
            $parent = $this->organizationModel->find($data['parend_id']);
            $data['level'] = $parent ? $parent['level'] + 1 : 1;
        } else {
            $data['level'] = 1;
            $data['parend_id'] = 0;
        }

        if ($this->organizationModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'แก้ไของค์กรสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถแก้ไของค์กรได้']);
        }
    }

    public function delete($uuid = null)
    {
        $organization = $this->organizationModel->where('uuid', $uuid)->first();

        $children = $this->organizationModel->where('parend_id', $organization['node_id'])->findAll();
        if (!empty($children)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถลบองค์กรที่มีองค์กรย่อยได้']);
        }

        if ($this->organizationModel->delete($organization['id'])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'ลบองค์กรสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถลบองค์กรได้']);
        }
    }

    public function move()
    {
        $data = $this->request->getJSON(true);
        $nodeId = $data['nodeId'];
        $targetId = $data['targetId'] ?? 0;
        $position = $data['position'] ?? 'child';

        $updateData = [];

        if ($position === 'child' && $targetId) {
            $target = $this->organizationModel->find($targetId);
            $updateData['parend_id'] = $targetId;
            $updateData['level'] = $target ? $target['level'] + 1 : 1;
        } else {
            $updateData['parend_id'] = 0;
            $updateData['level'] = 1;
        }

        if ($this->organizationModel->update($nodeId, $updateData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'ย้ายองค์กรสำเร็จ']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถย้ายองค์กรได้']);
        }
    }

    private function buildTree($organizations, $parentId = 0)
    {
        $tree = [];
        foreach ($organizations as $organization) {
            $orgParentId = $organization['parend_id'] ?? 0;
            if ($orgParentId == $parentId) {
                $children = $this->buildTree($organizations, $organization['node_id']);
                if ($children) {
                    $organization['children'] = $children;
                }
                $tree[] = $organization;
            }
        }
        return $tree;
    }

    private function buildFancytreeData($organizations, $parentId = 0)
    {
        $tree = [];
        foreach ($organizations as $organization) {
            $orgParentId = $organization['parend_id'] ?? 0;
            if ($orgParentId == $parentId) {
                $children = $this->buildFancytreeData($organizations, $organization['node_id']);
                $node = [
                    'key' => $organization['node_id'],
                    'title' => $organization['name'],
                    'data' => $organization,
                    'expanded' => true,
                    'folder' => !empty($children)
                ];
                if ($children) {
                    $node['children'] = $children;
                }
                $tree[] = $node;
            }
        }
        return $tree;
    }

    public function import_organization()
    {
        $file = $this->request->getFile('file');
        if (empty($file) || !$file->isValid()) {
            return $this->response->setJSON(['status' => 400, 'message' => 'Invalid file'])->setStatusCode(400);
        }

        $allowedExtensions = ['xlsx', 'xls'];
        if (!in_array($file->getClientExtension(), $allowedExtensions)) {
            return $this->response->setJSON(['status' => 400, 'message' => 'รองรับเฉพาะไฟล์ .xlsx และ .xls เท่านั้น'])->setStatusCode(400);
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (count($rows) <= 1) {
                return $this->response->setJSON(['status' => 400, 'message' => 'ไฟล์ Excel ไม่มีข้อมูล'])->setStatusCode(400);
            }

            $errors = [];
            $validData = [];
            $columnNames = ['ID', 'Parent ID', 'ชื่อองค์กร', 'ระดับ', 'ลำดับ', 'สถานะ'];

            $dataRows = array_slice($rows, 1);
            $isEmptyRow = function ($row) {
                for ($i = 0; $i < 6; $i++) {
                    if (isset($row[$i]) && $row[$i] !== null && $row[$i] !== '') {
                        return false;
                    }
                }
                return true;
            };

            $excelIds = [];
            $excelDataMap = [];
            foreach ($dataRows as $row) {
                if ($isEmptyRow($row)) {
                    continue;
                }

                if (!empty($row[0]) && is_numeric($row[0])) {
                    $excelIds[] = (string) $row[0];
                    $excelDataMap[$row[0]] = [
                        'id' => $row[0],
                        'parend_id' => $row[1] ?? 0,
                        'level' => $row[3] ?? 1
                    ];
                }
            }

            foreach ($dataRows as $rowIndex => $row) {
                $currentRow = $rowIndex + 2;

                if ($isEmptyRow($row)) {
                    continue;
                }

                $rowErrors = [];

                for ($i = 0; $i <= 5; $i++) {
                    if ($i == 1) {
                        if (!isset($row[$i]) || !is_numeric($row[$i])) {
                            $rowErrors[] = "คอลัมน์ {$columnNames[$i]} ไม่มีข้อมูล";
                        }
                    } else {
                        if (empty($row[$i]) && $row[$i] !== '0' && $row[$i] !== 0) {
                            $rowErrors[] = "คอลัมน์ {$columnNames[$i]} ไม่มีข้อมูล";
                        }
                    }
                }

                if (!empty($row[0]) && !is_numeric($row[0])) {
                    $rowErrors[] = "ID ต้องเป็นตัวเลข";
                }

                if (!empty($row[0]) && is_numeric($row[0]) && (intval($row[0]) <= 0 || intval($row[0]) != $row[0])) {
                    $rowErrors[] = "ID ต้องเป็นจำนวนเต็มบวก (1, 2, 3, ...)";
                }

                if (isset($row[1]) && !is_numeric($row[1])) {
                    $rowErrors[] = "Parent ID ต้องเป็นตัวเลข";
                }

                if (isset($row[1]) && is_numeric($row[1]) && (intval($row[1]) < 0 || intval($row[1]) != $row[1])) {
                    $rowErrors[] = "Parent ID ต้องเป็นจำนวนเต็มไม่ติดลบ (0, 1, 2, 3, ...)";
                }
                if (!empty($row[3]) && !is_numeric($row[3])) {
                    $rowErrors[] = "ระดับต้องเป็นตัวเลข";
                }
                if (!empty($row[4]) && !is_numeric($row[4])) {
                    $rowErrors[] = "ลำดับต้องเป็นตัวเลข";
                }

                if (!empty($row[1]) && $row[1] != 0) {
                    $parent = null;
                    $parentLevel = null;

                    $parentFromDB = $this->organizationModel->where('node_id', $row[1])->first();
                    if (!empty($parentFromDB)) {
                        $parent = $parentFromDB;
                        $parentLevel = $parentFromDB['level'];
                    } else if (in_array($row[1], $excelIds)) {
                        $parent = $excelDataMap[$row[1]];
                        $parentLevel = $excelDataMap[$row[1]]['level'];
                    }

                    if (empty($parent)) {
                        $rowErrors[] = "องค์กรหลัก ID {$row[1]} ไม่พบในระบบและไม่มีในไฟล์ Excel นี้";
                    } else {
                        if (!empty($row[3]) && is_numeric($parentLevel) && ($parentLevel + 1 != $row[3])) {
                            $parentSource = isset($parentFromDB) && !empty($parentFromDB) ? "ในระบบ" : "ในไฟล์ Excel";
                            $rowErrors[] = "ระดับองค์กรไม่ถูกต้อง (องค์กรหลัก {$parentSource} อยู่ระดับ {$parentLevel}, ควรเป็น " . ($parentLevel + 1) . ")";
                        }
                    }
                }

                if (!empty($row[2]) && strlen($row[2]) > 255) {
                    $rowErrors[] = "ชื่อองค์กรต้องมีค่าไม่เกิน 255 ตัวอักษร (ปัจจุบัน " . strlen($row[2]) . " ตัวอักษร)";
                }

                if (!empty($row[4]) && $row[4] < 1) {
                    $rowErrors[] = "ลำดับต้องมีค่ามากกว่า 0";
                }

                if (!empty($row[5]) && !in_array($row[5], ['active', 'inactive'])) {
                    $rowErrors[] = "สถานะต้องเป็น 'active' หรือ 'inactive' เท่านั้น";
                }

                if (!empty($row[0])) {
                    $existingOrg = $this->organizationModel->where('node_id', $row[0])->first();
                    if (!empty($existingOrg)) {
                        $rowErrors[] = "ID {$row[0]} มีอยู่ในระบบแล้ว";
                    }
                }

                if (!empty($row[0])) {
                    $filteredExcelIds = array_filter($excelIds, function ($value) {
                        return is_string($value) || is_int($value);
                    });

                    $duplicateCount = array_count_values($filteredExcelIds);
                    $currentId = (string) $row[0];

                    if (isset($duplicateCount[$currentId]) && $duplicateCount[$currentId] > 1) {
                        $rowErrors[] = "ID {$row[0]} ซ้ำในไฟล์ Excel ({$duplicateCount[$currentId]} ครั้ง)";
                    }
                }

                if (!empty($rowErrors)) {
                    $errors[] = [
                        'row' => $currentRow,
                        'data' => [
                            'node_id' => $row[0] ?? '',
                            'parend_id' => $row[1] ?? '',
                            'name' => $row[2] ?? '',
                            'level' => $row[3] ?? '',
                            'sort' => $row[4] ?? '',
                            'status' => $row[5] ?? ''
                        ],
                        'errors' => $rowErrors
                    ];
                } else {
                    $validData[] = [
                        'uuid' => Uuid::uuid4()->toString(),
                        'node_id' => $row[0],
                        'parend_id' => $row[1],
                        'name' => $row[2],
                        'level' => $row[3],
                        'sort' => $row[4],
                        'status' => $row[5]
                    ];
                }
            }

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'พบข้อผิดพลาดในไฟล์ Excel (' . count($errors) . ' แถว)',
                    'errors' => $errors,
                    'total_errors' => count($errors),
                    'total_rows' => count($dataRows)
                ])->setStatusCode(400);
            }

            usort($validData, function ($a, $b) {
                return $a['level'] <=> $b['level'];
            });

            $successCount = 0;
            $failedInserts = [];

            foreach ($validData as $organization) {
                try {
                    $sql = "INSERT INTO tb_organization (id, uuid, node_id, parend_id, name, level, sort, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

                    $result = $this->organizationModel->db->query($sql, [
                        $organization['node_id'],
                        $organization['uuid'],
                        $organization['node_id'],
                        $organization['parend_id'],
                        $organization['name'],
                        $organization['level'],
                        $organization['sort'],
                        $organization['status']
                    ]);

                    if ($result) {
                        $successCount++;
                    } else {
                        $failedInserts[] = "ID {$organization['node_id']}: ไม่สามารถบันทึกข้อมูลได้";
                    }
                } catch (\Exception $e) {
                    $failedInserts[] = "ID {$organization['node_id']}: " . $e->getMessage();
                }
            }

            if (!empty($failedInserts)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => "นำเข้าข้อมูลไม่สำเร็จ บางรายการไม่สามารถบันทึกได้",
                    'success_count' => $successCount,
                    'failed_count' => count($failedInserts),
                    'failed_details' => $failedInserts,
                    'total_rows' => count($validData)
                ])->setStatusCode(400);
            }

            if ($successCount > 0) {
                $maxId = max(array_column($validData, 'node_id'));
                $nextId = $maxId + 1;
                $this->organizationModel->db->query("ALTER TABLE tb_organization AUTO_INCREMENT = ?", [$nextId]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "นำเข้าข้อมูลสำเร็จ {$successCount} รายการ",
                'success_count' => $successCount,
                'total_rows' => count($validData)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Import organization error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 500, 'message' => 'เกิดข้อผิดพลาดในการอ่านไฟล์ Excel: ' . $e->getMessage()])->setStatusCode(500);
        }
    }

    public function export_organization()
    {
        try {
            $organizations = $this->organizationModel->orderBy('node_id', 'ASC')->findAll();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Organization Export');

            $header = [
                'A1' => 'รหัสองค์กร',
                'B1' => 'parent_id',
                'C1' => 'ชื่อองค์กร',
                'D1' => 'ระดับ',
                'E1' => 'ลำดับ',
                'F1' => 'สถานะ',
            ];

            foreach ($header as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            $row = 2;
            foreach ($organizations as $organization) {
                $sheet->setCellValue('A' . $row, $organization['node_id']);
                $sheet->setCellValue('B' . $row, $organization['parend_id']);
                $sheet->setCellValue('C' . $row, $organization['name']);
                $sheet->setCellValue('D' . $row, $organization['level']);
                $sheet->setCellValue('E' . $row, $organization['sort']);
                $sheet->setCellValue('F' . $row, $organization['status']);
                $row++;
            }

            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'organization_export_' . date('Ymd_His') . '.xlsx';

            $tempFile = tempnam(sys_get_temp_dir(), 'excel_export_');

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);

            $fileContent = file_get_contents($tempFile);

            unlink($tempFile);

            if ($fileContent === false || empty($fileContent)) {
                throw new Exception('ไม่สามารถสร้างไฟล์ Excel ได้');
            }

            while (ob_get_level()) {
                ob_end_clean();
            }

            $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $this->response->setHeader('Content-Length', strlen($fileContent));
            $this->response->setHeader('Cache-Control', 'max-age=0');
            $this->response->setHeader('Pragma', 'public');

            return $this->response->setBody($fileContent);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function download_template_organization()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Organization Template');

        $header = [
            'A1' => 'รหัสองค์กร',
            'B1' => 'parent_id',
            'C1' => 'ชื่อองค์กร',
            'D1' => 'ระดับ',
            'E1' => 'ลำดับ',
            'F1' => 'สถานะ',
        ];

        foreach ($header as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $organizations = [
            [
                'id' => 1001,
                'parend_id' => 0,
                'name' => 'บริษัทหลัก',
                'level' => 1,
                'sort' => 1,
                'status' => 'active'
            ],
            [
                'id' => 1002,
                'parend_id' => 1001,
                'name' => 'สาขากรุงเทพ',
                'level' => 2,
                'sort' => 2,
                'status' => 'active'
            ],
            [
                'id' => 1003,
                'parend_id' => 1001,
                'name' => 'สาขาเชียงใหม่',
                'level' => 2,
                'sort' => 3,
                'status' => 'active'
            ]
        ];

        $row = 2;
        foreach ($organizations as $organization) {
            $sheet->setCellValue('A' . $row, $organization['id']);
            $sheet->setCellValue('B' . $row, $organization['parend_id']);
            $sheet->setCellValue('C' . $row, $organization['name']);
            $sheet->setCellValue('D' . $row, $organization['level']);
            $sheet->setCellValue('E' . $row, $organization['sort']);
            $sheet->setCellValue('F' . $row, $organization['status']);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'organization_template_' . date('Ymd_His') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'excel_export_');

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $fileContent = file_get_contents($tempFile);

        unlink($tempFile);

        if ($fileContent === false || empty($fileContent)) {
            throw new \Exception('ไม่สามารถสร้างไฟล์ Excel ได้');
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Content-Length', strlen($fileContent));
        $this->response->setHeader('Cache-Control', 'max-age=0');
        $this->response->setHeader('Pragma', 'public');

        return $this->response->setBody($fileContent);
    }
}
