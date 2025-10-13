<?php

return [
    // Page titles and headers
    'manage_categories' => 'จัดการหมวดหมู่',
    'category_structure' => 'โครงสร้างหมวดหมู่',
    'room_management' => 'จัดการห้อง',
    'manage_structure_description' => 'จัดการโครงสร้างหมวดหมู่และหมวดหมู่ย่อย',
    
    // Stats labels
    'total_categories' => 'หมวดหมู่ทั้งหมด',
    'root_categories' => 'หมวดหมู่หลัก',
    'sub_categories' => 'หมวดหมู่ย่อย',
    'active_categories' => 'เปิดใช้งาน',
    'inactive_categories' => 'ปิดใช้งาน',
    
    // Form labels
    'category_name' => 'ชื่อหมวดหมู่',
    'category_status' => 'สถานะ',
    'sort_order' => 'ลำดับการแสดง',
    'parent_category' => 'หมวดหมู่หลัก',
    
    // Status options
    'active' => 'เปิดใช้งาน',
    'inactive' => 'ปิดใช้งาน',
    
    // Buttons
    'add_category' => 'เพิ่มหมวดหมู่',
    'add_first_category' => 'เพิ่มหมวดหมู่แรก',
    'add_subcategory' => 'เพิ่มหมวดหมู่ย่อย',
    'edit_category' => 'แก้ไขหมวดหมู่',
    'delete_category' => 'ลบหมวดหมู่',
    'save' => 'บันทึก',
    'cancel' => 'ยกเลิก',
    'close' => 'ปิด',
    'saving' => 'กำลังบันทึก...',
    
    // Brands management
    'available_brands' => 'แบรนต์ที่สามารถเพิ่มได้',
    'brands_in_category' => 'แบรนต์ในหมวดหมู่นี้',
    'add_selected_brands' => 'เพิ่มแบรนต์ที่เลือก',
    'remove_selected_brands' => 'ลบแบรนต์ที่เลือก',
    'search_brands' => 'ค้นหาแบรนต์...',
    
    // Context menu
    'manage_brands' => 'จัดการแบรนต์',
    'edit' => 'แก้ไข',
    'delete' => 'ลบ',
    
    // Help & Tips
    'tips' => 'คำแนะนำ',
    'right_click_tip' => 'คลิกขวาที่หมวดหมู่เพื่อแสดงเมนู',
    'add_subcategory_tip' => 'เพิ่มหมวดหมู่ย่อยใต้หมวดหมู่ที่เลือก',
    'drag_drop_tip' => 'ลากและวางเพื่อย้ายหมวดหมู่',
    'edit_tip' => 'แก้ไขชื่อและรายละเอียด',
    
    // Empty states
    'no_categories_yet' => 'ยังไม่มีหมวดหมู่',
    'start_by_adding' => 'เริ่มต้นโดยการเพิ่มหมวดหมู่แรก',
    'no_brands_available' => 'ไม่มีแบรนต์ที่สามารถเพิ่มได้',
    'no_brands_in_category' => 'ยังไม่มีแบรนต์ในหมวดหมู่นี้',
    
    // Placeholders
    'enter_category_name' => 'กรอกชื่อหมวดหมู่',
    'sort_number' => 'ลำดับ',
    
    // Confirmations
    'confirm_delete' => 'ยืนยันการลบ',
    'delete_category_message' => 'คุณต้องการลบหมวดหมู่นี้ใช่หรือไม่?',
    'delete_brands_message' => 'คุณต้องการลบแบรนต์ออกจากหมวดหมู่นี้ใช่หรือไม่?',
    'yes_delete' => 'ใช่, ลบ',
    
    // Alerts
    'please_select_brands_to_add' => 'กรุณาเลือกแบรนต์ที่ต้องการเพิ่ม',
    'please_select_brands_to_remove' => 'กรุณาเลือกแบรนต์ที่ต้องการลบ',
    
    // Success messages
    'success' => [
        'category_created' => 'เพิ่มหมวดหมู่สำเร็จ',
        'category_updated' => 'อัปเดตหมวดหมู่สำเร็จ',
        'category_deleted' => 'ลบหมวดหมู่สำเร็จ',
        'category_moved' => 'ย้ายหมวดหมู่สำเร็จ',
        'brands_added' => 'เพิ่มแบรนต์ในหมวดหมู่สำเร็จ',
        'brands_removed' => 'ลบแบรนต์ออกจากหมวดหมู่สำเร็จ',
        'brands_added_count' => 'เพิ่มแบรนต์เข้าหมวดหมู่สำเร็จ {0} แบรนต์',
        'brands_removed_count' => 'ลบแบรนต์ออกจากหมวดหมู่สำเร็จ {0} แบรนต์'
    ],
    
    // Error messages
    'error' => [
        'category_not_found' => 'ไม่พบหมวดหมู่ที่ระบุ',
        'create_category' => 'เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่',
        'update_category' => 'เกิดข้อผิดพลาดในการอัปเดตหมวดหมู่',
        'delete_category' => 'เกิดข้อผิดพลาดในการลบหมวดหมู่',
        'move_category' => 'เกิดข้อผิดพลาดในการย้ายหมวดหมู่',
        'load_data' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล',
        'add_brands' => 'เกิดข้อผิดพลาดในการเพิ่มแบรนต์',
        'remove_brands' => 'เกิดข้อผิดพลาดในการลบแบรนต์',
        'save_data' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล',
        'invalid_data' => 'ข้อมูลไม่ถูกต้อง',
        'permission_denied' => 'คุณไม่มีสิทธิ์ในการดำเนินการนี้',
        'cannot_delete_with_children' => 'ไม่สามารถลบหมวดหมู่ที่มีหมวดหมู่ย่อยได้',
        'category_id_required' => 'จำเป็นต้องระบุ ID หมวดหมู่',
        'category_brand_ids_required' => 'จำเป็นต้องระบุ ID หมวดหมู่และ ID แบรนต์',
        'some_errors_occurred' => '(มีข้อผิดพลาดบางรายการ)'
    ],
    
    // Validation messages
    'validation' => [
        'name_required' => 'กรุณากรอกชื่อหมวดหมู่',
        'name_max_length' => 'ชื่อหมวดหมู่ต้องไม่เกิน 255 ตัวอักษร',
        'sort_must_be_number' => 'ลำดับการแสดงต้องเป็นตัวเลข',
        'sort_min_value' => 'ลำดับการแสดงต้องมากกว่า 0'
    ]
];