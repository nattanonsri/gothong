<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\AttachmentModel;
use App\Models\CounterpartieModel;
use App\Models\PaymentModel;
use App\Models\CategoryModel;
use Ramsey\Uuid\Uuid;
use Config\Database;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use PhpParser\Node\Expr\FuncCall;

class RecordController extends BaseController
{
    protected $transactionModel , $transactionItemModel, $attachmentModel, $counterpartieModel, $paymentModel, $categoryModel;
    protected $db;
    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->transactionItemModel = new TransactionItemModel();
        $this->attachmentModel = new AttachmentModel();
        $this->counterpartieModel = new CounterpartieModel();
        $this->paymentModel = new PaymentModel();
        $this->categoryModel = new CategoryModel();
        $this->db = Database::connect();
    }

    public function index()
    {
        $data['payments'] = $this->paymentModel->findAll();
        $data['categories'] = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $data['counterparties'] = $this->counterpartieModel->orderBy('name', 'ASC')->findAll();
        // dd($data['counterparties']);
        return view('backend/records/manage_record', $data);
    }

    public function list_transactions()
    {
        $request = $this->request->getPost();
        
        $start = intval($request['start'] ?? 0);
        $length = intval($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';
        $orderColumnIndex = intval($request['order'][0]['column'] ?? 0);
        $orderDir = $request['order'][0]['dir'] ?? 'desc';

        $columns = ['id', 'datetime', 'ref_no', 'total', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $builder = $this->transactionModel
            ->select('tb_transaction.*, tb_counterpartie.name as counterpartie_name, tb_payment.name_th as payment_name')
            ->join('tb_counterpartie', 'tb_counterpartie.id = tb_transaction.counterpartie_id', 'left')
            ->join('tb_payment', 'tb_payment.id = tb_transaction.payment_id', 'left');

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('tb_transaction.ref_no', $searchValue)
                ->orLike('tb_transaction.descripton', $searchValue)
                ->orLike('tb_counterpartie.name', $searchValue)
                ->groupEnd();
        }

        $totalRecords = $this->transactionModel->countAllResults(false);
        $filteredRecords = $builder->countAllResults(false);

        $transactions = $builder
            ->orderBy($orderColumn, $orderDir)
            ->limit($length, $start)
            ->find();

        return $this->response->setJSON([
            'draw' => intval($request['draw'] ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $transactions
        ]);
    }

    public function get_transaction($uuid)
    {
        $transaction = $this->transactionModel
            ->select('tb_transaction.*, tb_counterpartie.name as counterpartie_name, tb_payment.name_th as payment_name')
            ->join('tb_counterpartie', 'tb_counterpartie.id = tb_transaction.counterpartie_id', 'left')
            ->join('tb_payment', 'tb_payment.id = tb_transaction.payment_id', 'left')
            ->where('tb_transaction.uuid', $uuid)
            ->first();

        if (empty($transaction)) {
            return $this->response->setJSON(['status' => 400, 'message' => 'ไม่พบข้อมูล'])->setStatusCode(400);
        }

        $items = $this->transactionItemModel
            ->select('tb_transaction_item.*, tb_category.name as category_name')
            ->join('tb_category', 'tb_category.id = tb_transaction_item.category_id', 'left')
            ->where('transaction_id', $transaction['id'])
            ->findAll();

        $attachments = $this->attachmentModel
            ->where('transaction_id', $transaction['id'])
            ->findAll();

        $transaction['items'] = $items;
        $transaction['attachments'] = $attachments;

        return $this->response->setJSON([
            'status' => 200,
            'data' => $transaction
        ]);
    }

    public function create()
    {
        try {
        
            $this->db->transStart();

            $data = $this->request->getPost(); 

            $counterpartieId = $this->handleCounterpartie($data);
       
            $transactionData = [
                'uuid' => Uuid::uuid4()->toString(),
                'datetime' => $data['datetime'] ?? date('Y-m-d H:i:s'),
                'counterpartie_id' => $counterpartieId,
                'payment_id' => $data['payment_id'] ?? null,
                'user_id' => USER_ID,
                'ref_no' => $data['ref_no'] ?? '',
                'descripton' => $data['description'] ?? '',
                'total' => $data['total'] ?? 0
            ];
            $transactionId = $this->transactionModel->insert($transactionData);
   

            if (empty($transactionId)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่สามารถบันทึกข้อมูลได้'
                ])->setStatusCode(400);
            }

            if (!empty($data['items'])) {
                $items = json_decode($data['items'], true);
                foreach ($items as $item) {
                    $itemData = [
                        'uuid' => Uuid::uuid4()->toString(),
                        'transaction_id' => $transactionId,
                        'category_id' => $item['category_id'] ?? null,
                        'name' => $item['name'] ?? '',
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'note' => $item['note'] ?? ''
                    ];
                    $this->transactionItemModel->insert($itemData);
                   
                }
            }

            $this->handleAttachments($transactionId);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'
                ])->setStatusCode(400);
            }

            add_log(USER_ID, 'POST', '/backend/record/create', $data);

            return $this->response->setJSON([
                'status' => 200,
                'message' => 'บันทึกข้อมูลสำเร็จ'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function update($uuid)
    {
        try {
            $this->db->transStart();

            $transaction = $this->transactionModel->where('uuid', $uuid)->first();
            if (empty($transaction)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่พบข้อมูล'
                ])->setStatusCode(400);
            }

            $data = $this->request->getPost();
            
            $counterpartieId = $this->handleCounterpartie($data);
            
            $transactionData = [
                'datetime' => $data['datetime'] ?? $transaction['datetime'],
                'counterpartie_id' => $counterpartieId,
                'payment_id' => $data['payment_id'] ?? $transaction['payment_id'],
                'ref_no' => $data['ref_no'] ?? $transaction['ref_no'],
                'descripton' => $data['description'] ?? $transaction['descripton'],
                'total' => $data['total'] ?? $transaction['total']
            ];

            $this->transactionModel->update($transaction['id'], $transactionData);

            $this->transactionItemModel->where('transaction_id', $transaction['id'])->delete();
            
            if (!empty($data['items'])) {
                $items = json_decode($data['items'], true);
                foreach ($items as $item) {
                    $itemData = [
                        'uuid' => Uuid::uuid4()->toString(),
                        'transaction_id' => $transaction['id'],
                        'category_id' => $item['category_id'] ?? null,
                        'name' => $item['name'] ?? '',
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'note' => $item['note'] ?? ''
                    ];
                    $this->transactionItemModel->insert($itemData);
                }
            }

            $this->handleAttachments($transaction['id']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล'
                ])->setStatusCode(400);
            }
            add_log(USER_ID, 'PUT', "/backend/record/update/{$uuid}", $data);
            return $this->response->setJSON([
                'status' => 200,
                'message' => 'แก้ไขข้อมูลสำเร็จ'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    public function delete($uuid)
    {
        try {
            $transaction = $this->transactionModel->where('uuid', $uuid)->first();
            if (empty($transaction)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่พบข้อมูล'
                ])->setStatusCode(400);
            }

            $attachments = $this->attachmentModel->where('transaction_id', $transaction['id'])->findAll();
            foreach ($attachments as $attachment) {
                if (file_exists($attachment['file_path'])) {
                    unlink($attachment['file_path']);
                }
            }

            $this->transactionModel->delete($transaction['id']);
            add_log(USER_ID, 'DELETE', "/backend/record/delete/{$uuid}");

            return $this->response->setJSON([
                'status' => 200,
                'message' => 'ลบข้อมูลสำเร็จ'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function delete_attachment($uuid)
    {
        try {
            $attachment = $this->attachmentModel->where('uuid', $uuid)->first();
            if (empty($attachment)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่พบไฟล์'
                ])->setStatusCode(400);
            }

            if (file_exists($attachment['file_path'])) {
                $path = WRITEPATH . 'uploads/transactions/' . $attachment['file_path'];
                unlink($path);
            }

            $this->attachmentModel->delete($attachment['id']);

            return $this->response->setJSON([
                'status' => 200,
                'message' => 'ลบไฟล์สำเร็จ'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function get_counterparties()
    {
        $type = $this->request->getGet('type');
        $builder = $this->counterpartieModel;
        
        if ($type) {
            $builder->where('type', $type);
        }
        
        $counterparties = $builder->findAll();
        
        return $this->response->setJSON([
            'status' => 200,
            'data' => $counterparties
        ]);
    }

    private function handleCounterpartie($data)
    {
        if (!empty($data['counterpartie_id']) && $data['counterpartie_id'] !== 'new') {
            return $data['counterpartie_id'];
        }

        if (!empty($data['counterpartie_name']) && $data['counterpartie_id'] === 'new') {
            $counterpartieData = [
                'uuid' => Uuid::uuid4()->toString(),
                'type' => $data['counterpartie_type'] ?? 'cash',
                'name' => $data['counterpartie_name'],
                'tax_id' => $data['counterpartie_tax_id'] ?? '',
                'phone' => $data['counterpartie_phone'] ?? '',
                'email' => $data['counterpartie_email'] ?? ''
            ];
            $counterpartieId = $this->counterpartieModel->insert($counterpartieData);
            if (empty($counterpartieId)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่สามารถบันทึกข้อมูลได้'
                ])->setStatusCode(400);
            }
            return $counterpartieId;
        }

        return null;
    }

    private function handleAttachments($transactionId)
    {
        $files = $this->request->getFiles();
        
        if (!empty($files['attachments'])) {
            foreach ($files['attachments'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $uploadPath = WRITEPATH . 'uploads/transactions/';
                    
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    $file->move($uploadPath, $newName);
                    
                    $attachmentData = [
                        'uuid' => Uuid::uuid4()->toString(),
                        'transaction_id' => $transactionId,
                        'file_name' => $file->getClientName(),
                        'file_path' => $newName
                    ];
                    
                    $attachmentId = $this->attachmentModel->insert($attachmentData);
                    if (empty($attachmentId)) {
                        return $this->response->setJSON([
                            'status' => 400,
                            'message' => 'ไม่สามารถบันทึกข้อมูลได้'
                        ])->setStatusCode(400);
                    }
                }
            }
        }
    }

    public function get_images($path) {

        $path = WRITEPATH . 'uploads/transactions/' . $path;

        $mimeType = mime_content_type($path);
        $response = $this->response->setContentType($mimeType);
        $response->setBody(file_get_contents($path));
        return $response;

       if(!file_exists($path)) { 
        return $this->response->setStatusCode(400)->setJSON([
            'status' => 400,
            'message' => 'ไม่พบรูปภาพ'
        ]);
       }

    }
}

