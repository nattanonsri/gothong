<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PaymentModel;
use App\Models\TransactionModel;
use Ramsey\Uuid\Uuid;

class PaymentController extends BaseController
{
    protected $paymentModel, $transactionModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->transactionModel = new TransactionModel();
    }

    public function index()
    {
        return view('backend/payments/manage_payment');
    }

    public function list_payments()
    {
        $request = $this->request->getPost();

        $start = intval($request['start'] ?? 0);
        $length = intval($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';
        $orderColumnIndex = intval($request['order'][0]['column'] ?? 0);
        $orderDir = $request['order'][0]['dir'] ?? 'desc';

        $columns = ['id', 'type', 'code', 'name_th', 'name_en'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $builder = $this->paymentModel;

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('type', $searchValue)
                ->orLike('code', $searchValue)
                ->orLike('name_th', $searchValue)
                ->orLike('name_en', $searchValue)
                ->groupEnd();
        }

        $totalRecords = $this->paymentModel->countAllResults(false);
        $filteredRecords = $builder->countAllResults(false);

        $payments = $builder
            ->orderBy($orderColumn, $orderDir)
            ->limit($length, $start)
            ->find();

        return $this->response->setJSON([
            'draw' => intval($request['draw'] ?? 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $payments
        ]);
    }

    public function get_payment($uuid)
    {
        $payment = $this->paymentModel->where('uuid', $uuid)->first();
        if (empty($payment)) {
            return $this->response->setJSON(['status' => 400, 'message' => 'ไม่พบข้อมูล'])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'status' => 200,
            'data' => $payment
        ]);
    }

    public function create()
    {
        try {
            $data = $this->request->getPost();

            if (empty($data['type'])) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'กรุณาเลือกประเภท'
                ])->setStatusCode(400);
            }

            if (empty($data['name_th'])) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'กรุณากรอกชื่อภาษาไทย'
                ])->setStatusCode(400);
            }

            if (!empty($data['code'])) {
                $existing = $this->paymentModel->where('code', $data['code'])->first();

                if (!empty($existing)) {
                    return $this->response->setJSON([
                        'status' => 400,
                        'message' => 'รหัสนี้มีอยู่ในระบบแล้ว'
                    ])->setStatusCode(400);
                }
            }

            $paymentData = [
                'uuid' => Uuid::uuid4()->toString(),
                'type' => $data['type'],
                'code' => $data['code'] ?? '',
                'name_th' => $data['name_th'],
                'name_en' => $data['name_en'] ?? ''
            ];
            $paymentId = $this->paymentModel->insert($paymentData);
            if (!empty($paymentId)) {
                add_log(USER_ID, 'POST', '/payment/create', $data);

                return $this->response->setJSON([
                    'status' => 200,
                    'message' => 'เพิ่มข้อมูลสำเร็จ'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่สามารถบันทึกข้อมูลได้'
                ])->setStatusCode(400);
            }
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
            $payment = $this->paymentModel->where('uuid', $uuid)->first();
            if (empty($payment)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่พบข้อมูล'
                ])->setStatusCode(400);
            }

            $data = $this->request->getPost();

            if (empty($data['type'])) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'กรุณาเลือกประเภท'
                ])->setStatusCode(400);
            }

            if (empty($data['name_th'])) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'กรุณากรอกชื่อภาษาไทย'
                ])->setStatusCode(400);
            }

            if (!empty($data['code'])) {
                $existing = $this->paymentModel
                    ->where('code', $data['code'])
                    ->where('id !=', $payment['id'])
                    ->first();
                if ($existing) {
                    return $this->response->setJSON([
                        'status' => 400,
                        'message' => 'รหัสนี้มีอยู่ในระบบแล้ว'
                    ])->setStatusCode(400);
                }
            }

            $paymentData = [
                'type' => $data['type'],
                'code' => $data['code'] ?? '',
                'name_th' => $data['name_th'],
                'name_en' => $data['name_en'] ?? ''
            ];

            $paymentId = $this->paymentModel->update($payment['id'], $paymentData);

            if (!empty($paymentId)) {
                add_log(USER_ID, 'PUT', "/payment/update/{$uuid}", $data);

                return $this->response->setJSON([
                    'status' => 200,
                    'message' => 'แก้ไขข้อมูลสำเร็จ'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่สามารถแก้ไขข้อมูลได้'
                ])->setStatusCode(400);
            }
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
            $payment = $this->paymentModel->where('uuid', $uuid)->first();
            if (empty($payment)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่พบข้อมูล'
                ])->setStatusCode(400);
            }

            $isUsed = $this->transactionModel->where('payment_id', $payment['id'])->first();
            if (!empty($isUsed)) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่สามารถลบได้ เนื่องจากมีการใช้งานอยู่'
                ])->setStatusCode(400);
            }

            $paymentId = $this->paymentModel->delete($payment['id']);

            if (!empty($paymentId)) {
                add_log(USER_ID, 'DELETE', "/payment/delete/{$uuid}", []);

                return $this->response->setJSON([
                    'status' => 200,
                    'message' => 'ลบข้อมูลสำเร็จ'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => 'ไม่สามารถลบข้อมูลได้'
                ])->setStatusCode(400);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
