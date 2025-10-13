<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CategoryModel;
use App\Models\BrandModel;
use App\Models\BrandCategoryModel;
use Ramsey\Uuid\Uuid;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Exception;


class CategoryController extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $categories = $this->categoryModel->orderBy('sort', 'ASC')->findAll();
        $data['categories'] = $this->buildTree($categories);
        $data['tree_json'] = json_encode($this->buildFancytreeData($categories));

        // Calculate statistics
        $data['stats'] = $this->categoryModel->calculateStats($categories);

        return view('backend/categorys/manage_category', $data);
    }

    public function get_tree_data()
    {
        $categories = $this->categoryModel->orderBy('sort', 'ASC')->findAll();
        return $this->response->setJSON($this->buildFancytreeData($categories));
    }

    public function create()
    {
        $data = $this->request->getPost();

        $data['uuid'] = Uuid::uuid4()->toString();
        if (!empty($data['parent_id']) && $data['parent_id'] != 0) {
            $parent = $this->categoryModel->find($data['parent_id']);
            $data['level'] = $parent ? $parent['level'] + 1 : 1;
        } else {
            $data['level'] = 1;
            $data['parent_id'] = 0;
        }

        if (empty($data['sort'])) {
            $maxSort = $this->categoryModel->selectMax('sort')->first();
            $data['sort'] = $maxSort ? $maxSort['sort'] + 1 : 1;
        }

        $maxNode = $this->categoryModel->selectMax('node_id')->first();
        $data['node_id'] = $maxNode ? $maxNode['node_id'] + 1 : 1;

        if ($this->categoryModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('category.success.category_created')]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.create_category')]);
        }
    }

    public function update($id = null)
    {
        $data = $this->request->getPost();

        // Set level based on parent
        if (!empty($data['parent_id']) && $data['parent_id'] != 0) {
            $parent = $this->categoryModel->find($data['parent_id']);
            $data['level'] = $parent ? $parent['level'] + 1 : 1;
        } else {
            $data['level'] = 1;
            $data['parent_id'] = 0;
        }

        if ($this->categoryModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('category.success.category_updated')]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.update_category')]);
        }
    }

    public function delete($uuid = null)
    {

        $category = $this->categoryModel->where('uuid', $uuid)->first();

        $children = $this->categoryModel->where('parent_id', $category['node_id'])->findAll();
        if (!empty($children)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.cannot_delete_with_children')]);
        }

        if ($this->categoryModel->delete($category['id'])) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('category.success.category_deleted')]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.delete_category')]);
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
            $target = $this->categoryModel->find($targetId);
            $updateData['parent_id'] = $targetId;
            $updateData['level'] = $target ? $target['level'] + 1 : 1;
        } else {
            $updateData['parent_id'] = 0;
            $updateData['level'] = 1;
        }

        if ($this->categoryModel->update($nodeId, $updateData)) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('category.success.category_moved')]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.move_category')]);
        }
    }

    private function buildTree($categories, $parentId = 0)
    {
        $tree = [];
        foreach ($categories as $category) {
            $categoryParentId = $category['parent_id'] ?? 0;
            if ($categoryParentId == $parentId) {
                $children = $this->buildTree($categories, $category['node_id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $tree[] = $category;
            }
        }
        return $tree;
    }

    private function buildFancytreeData($categories, $parentId = 0)
    {
        $tree = [];
        foreach ($categories as $category) {
            $categoryParentId = $category['parent_id'] ?? 0;
            if ($categoryParentId == $parentId) {
                $children = $this->buildFancytreeData($categories, $category['node_id']);
                $node = [
                    'key' => $category['node_id'],
                    'title' => $category['name'],
                    'data' => $category,
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

    public function manage_category_brands($node_id = null)
    {
        if (!$node_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.category_id_required')]);
        }

        $category = $this->categoryModel->where('node_id', $node_id)->first();
        if (!$category) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.category_not_found')]);
        }

        $brandsInCategory = $this->brandCategoryModel
            ->select('tb_brand.*')
            ->join('tb_brand', 'tb_brand.id = tb_category_brand.brand_id')
            ->where('tb_category_brand.category_id', $category['id'])
            // ->where('tb_brand.status', 'active')
            ->findAll();

        $brandsInCategoryIds = array_column($brandsInCategory, 'id');
        $availableBrandsQuery = $this->brandModel->where('name !=', '')->where('name IS NOT NULL')->orderBy('name', 'ASC');

        if (!empty($brandsInCategoryIds)) {
            $availableBrandsQuery->whereNotIn('id', $brandsInCategoryIds);
        }
        $availableBrands = $availableBrandsQuery->findAll();

        return $this->response->setJSON([
            'status' => 200,
            'data' => [
                'category' => $category,
                'brands_in_category' => $brandsInCategory,
                'available_brands' => $availableBrands
            ]
        ]);
    }

    public function add_brands_category()
    {
        $nodeId = $this->request->getPost('node_id');
        if (!$nodeId) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.category_brand_ids_required')])->setStatusCode(400);
        }

        $successCount = 0;
        $errors = [];

        if ($successCount > 0) {
            $message = lang('category.success.brands_added_count', [$successCount]);
            if (!empty($errors)) {
                $message .= ' ' . lang('category.error.some_errors_occurred');
            }
            return $this->response->setJSON(['status' => 'success', 'message' => $message]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.add_brands')])->setStatusCode(400);
        }
    }

    public function remove_brands_from_category()
    {
        $nodeId = $this->request->getPost('node_id');

        if (!$nodeId) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.category_brand_ids_required')]);
        }

        $successCount = 0;


        if ($successCount > 0) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('category.success.brands_removed_count', [$successCount])
            ]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('category.error.remove_brands')]);
        }
    }



    public function import_category()
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
            $columnNames = ['ID', 'Parent ID', 'ชื่อหมวดหมู่', 'ระดับ', 'ลำดับ', 'สถานะ'];

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
                        'parent_id' => $row[1] ?? 0,
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

                // ตรวจสอบค่า ID ต้องเป็นจำนวนเต็มบวก
                if (!empty($row[0]) && is_numeric($row[0]) && (intval($row[0]) <= 0 || intval($row[0]) != $row[0])) {
                    $rowErrors[] = "ID ต้องเป็นจำนวนเต็มบวก (1, 2, 3, ...)";
                }

                if (isset($row[1]) && !is_numeric($row[1])) {
                    $rowErrors[] = "Parent ID ต้องเป็นตัวเลข";
                }

                // ตรวจสอบ Parent ID ต้องเป็นจำนวนเต็มไม่ติดลบ (0 หรือ บวก)
                if (isset($row[1]) && is_numeric($row[1]) && (intval($row[1]) < 0 || intval($row[1]) != $row[1])) {
                    $rowErrors[] = "Parent ID ต้องเป็นจำนวนเต็มไม่ติดลบ (0, 1, 2, 3, ...)";
                }
                if (!empty($row[3]) && !is_numeric($row[3])) {
                    $rowErrors[] = "ระดับต้องเป็นตัวเลข";
                }
                if (!empty($row[4]) && !is_numeric($row[4])) {
                    $rowErrors[] = "ลำดับต้องเป็นตัวเลข";
                }

                // ตรวจสอบ Parent ID ที่มีอยู่ในระบบหรือในไฟล์ Excel
                if (!empty($row[1]) && $row[1] != 0) {
                    $parent = null;
                    $parentLevel = null;

                    // ตรวจสอบในฐานข้อมูลก่อน (ใช้ node_id แทน id)
                    $parentFromDB = $this->categoryModel->where('node_id', $row[1])->first();
                    if (!empty($parentFromDB)) {
                        $parent = $parentFromDB;
                        $parentLevel = $parentFromDB['level'];
                    }
                    // ถ้าไม่มีในฐานข้อมูล ตรวจสอบในไฟล์ Excel
                    else if (in_array($row[1], $excelIds)) {
                        $parent = $excelDataMap[$row[1]];
                        $parentLevel = $excelDataMap[$row[1]]['level'];
                    }

                    if (empty($parent)) {
                        $rowErrors[] = "หมวดหมู่หลัก ID {$row[1]} ไม่พบในระบบและไม่มีในไฟล์ Excel นี้";
                    } else {
                        // ตรวจสอบระดับที่ถูกต้อง
                        if (!empty($row[3]) && is_numeric($parentLevel) && ($parentLevel + 1 != $row[3])) {
                            $parentSource = isset($parentFromDB) && !empty($parentFromDB) ? "ในระบบ" : "ในไฟล์ Excel";
                            $rowErrors[] = "ระดับหมวดหมู่ไม่ถูกต้อง (หมวดหมู่หลัก {$parentSource} อยู่ระดับ {$parentLevel}, ควรเป็น " . ($parentLevel + 1) . ")";
                        }
                    }
                }

                // ตรวจสอบความยาวชื่อหมวดหมู่
                if (!empty($row[2]) && strlen($row[2]) > 255) {
                    $rowErrors[] = "ชื่อหมวดหมู่ต้องมีค่าไม่เกิน 255 ตัวอักษร (ปัจจุบัน " . strlen($row[2]) . " ตัวอักษร)";
                }

                // ตรวจสอบค่าลำดับ
                if (!empty($row[4]) && $row[4] < 1) {
                    $rowErrors[] = "ลำดับต้องมีค่ามากกว่า 0";
                }

                // ตรวจสอบสถานะ
                if (!empty($row[5]) && !in_array($row[5], ['active', 'inactive'])) {
                    $rowErrors[] = "สถานะต้องเป็น 'active' หรือ 'inactive' เท่านั้น";
                }

                // ตรวจสอบ ID ซ้ำในฐานข้อมูล (ใช้ node_id)
                if (!empty($row[0])) {
                    $existingCategory = $this->categoryModel->where('node_id', $row[0])->first();
                    if (!empty($existingCategory)) {
                        $rowErrors[] = "ID {$row[0]} มีอยู่ในระบบแล้ว";
                    }
                }

                // ตรวจสอบ ID ซ้ำในไฟล์ Excel
                if (!empty($row[0])) {
                    // กรองเฉพาะค่าที่เป็น string หรือ integer เท่านั้น
                    $filteredExcelIds = array_filter($excelIds, function ($value) {
                        return is_string($value) || is_int($value);
                    });

                    $duplicateCount = array_count_values($filteredExcelIds);
                    $currentId = (string) $row[0]; // แปลงเป็น string เพื่อให้ตรงกับที่เก็บใน $excelIds

                    if (isset($duplicateCount[$currentId]) && $duplicateCount[$currentId] > 1) {
                        $rowErrors[] = "ID {$row[0]} ซ้ำในไฟล์ Excel ({$duplicateCount[$currentId]} ครั้ง)";
                    }
                }

                if (!empty($rowErrors)) {
                    $errors[] = [
                        'row' => $currentRow,
                        'data' => [
                            'node_id' => $row[0] ?? '',
                            'parent_id' => $row[1] ?? '',
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
                        'parent_id' => $row[1],
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

            foreach ($validData as $category) {
                try {
                    $sql = "INSERT INTO tb_category (id, uuid, node_id, parent_id, name, level, sort, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

                    $result = $this->categoryModel->db->query($sql, [
                        $category['node_id'], // ใช้ node_id เป็น id ด้วย
                        $category['uuid'],
                        $category['node_id'],
                        $category['parent_id'],
                        $category['name'],
                        $category['level'],
                        $category['sort'],
                        $category['status']
                    ]);

                    if ($result) {
                        $successCount++;
                    } else {
                        $failedInserts[] = "ID {$category['node_id']}: ไม่สามารถบันทึกข้อมูลได้";
                    }
                } catch (\Exception $e) {
                    $failedInserts[] = "ID {$category['node_id']}: " . $e->getMessage();
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
                $this->categoryModel->db->query("ALTER TABLE tb_category AUTO_INCREMENT = ?", [$nextId]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "นำเข้าข้อมูลสำเร็จ {$successCount} รายการ",
                'success_count' => $successCount,
                'total_rows' => count($validData)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Import category error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 500, 'message' => 'เกิดข้อผิดพลาดในการอ่านไฟล์ Excel: ' . $e->getMessage()])->setStatusCode(500);
        }
    }

    public function export_category()
    {
        try {
            $categories = $this->categoryModel->orderBy('node_id', 'ASC')->findAll();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Category Export');

            $header = [
                'A1' => 'รหัสหมวดหมู่',
                'B1' => 'parent_id',
                'C1' => 'ชื่อหมวดหมู่',
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
            foreach ($categories as $category) {
                $sheet->setCellValue('A' . $row, $category['node_id']); // ใช้ node_id แทน id
                $sheet->setCellValue('B' . $row, $category['parent_id']);
                $sheet->setCellValue('C' . $row, $category['name']);
                $sheet->setCellValue('D' . $row, $category['level']);
                $sheet->setCellValue('E' . $row, $category['sort']);
                $sheet->setCellValue('F' . $row, $category['status']);
                $row++;
            }

            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'category_export_' . date('Ymd_His') . '.xlsx';

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
    public function download_template_category()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Category Template');

        $header = [
            'A1' => 'รหัสหมวดหมู่',
            'B1' => 'parent_id',
            'C1' => 'ชื่อหมวดหมู่',
            'D1' => 'ระดับ',
            'E1' => 'ลำดับ',
            'F1' => 'สถานะ',
        ];

        foreach ($header as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $categories = [
            [
                'id' => 1001,
                'parent_id' => 0,
                'name' => 'computer',
                'level' => 1,
                'sort' => 1,
                'status' => 'active'
            ],
            [
                'id' => 1002,
                'parent_id' => 1001,
                'name' => 'laptop',
                'level' => 2,
                'sort' => 2,
                'status' => 'active'
            ],
            [
                'id' => 1003,
                'parent_id' => 1001,
                'name' => 'desktop',
                'level' => 2,
                'sort' => 3,
                'status' => 'active'
            ]
        ];

        $row = 2;
        foreach ($categories as $category) {
            $sheet->setCellValue('A' . $row, $category['id']);
            $sheet->setCellValue('B' . $row, $category['parent_id']);
            $sheet->setCellValue('C' . $row, $category['name']);
            $sheet->setCellValue('D' . $row, $category['level']);
            $sheet->setCellValue('E' . $row, $category['sort']);
            $sheet->setCellValue('F' . $row, $category['status']);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'category_export_' . date('Ymd_His') . '.xlsx';

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

    public function get_where_cat()
    {
        $uuid = esc($this->request->getPost('uuid'));
        $brand = $this->brandModel->where('uuid', $uuid)->first();
        $brandCategory = $this->brandCategoryModel->groupCat($brand['id']);

        if ($brandCategory) {
            return $this->response->setJSON(['status' => 200, 'data' => $brandCategory]);
        } else {
            return $this->response->setJSON(['status' => 400])->setStatusCode(400);
        }
    }
}
