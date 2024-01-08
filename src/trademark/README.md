## SETUP

 1. Tạo mới file **.env**, copy nội dung **.env.example** sang và thay đổi giá trị ``DB_DATABASE`` thành database của mình
 2. Chạy ``composer install`` để cài đặt các package **composer**
 3. Chạy ``php artisan key:generate`` để sinh key cho ứng dụng
 4. Chạy ``php artisan migrate`` để tạo các bảng trong database
 5. Chạy ``php artisan db:seed`` để sinh dữ liệu cho database

## UPDATE
 1. Checkout branch ``develop`` và pull code mới nhất về
 2. Chạy ``composer dump-autoload`` để autoload các class mới
 3. Chạy ``php artisan migrate:refresh --seed`` để refresh lại database

## USING AND EXAMPLE.
  1. Các hàm thông dụng:
  > ```$this->repository->list($conditions, $relation, $relationCounts)``` lấy list và relation theo dữ liệu đầu vào.
   - ``$conditions``: mảng các điều kiện.
   - ``$relation``: mảng realtion cần trả về.
   - ``$relationCounts``: mảng realtion cần trả về số lượng.
   *Chú ý:*
   - Việc xử lý điều kiện truyền vào nằm trong hàm `mergeQuery` của repository tương ứng.

  ```php
    public function mergeQuery($query, $column, $data)
    {
        switch ($column) {
            case 'type':
                return $query->where($column, $data);
            case 'name':
                return $query->whereRaw("concat(last_name, '', first_name) like '%$data%' ");
            case 'email':
            case 'username':
                return $query->where($column, 'like', '%' . $data . '%');
                break;
            default:
                return $query;
                break;
        }
    }
  ```
  **Lưu ý:**
  - Trong trường hợp cần phải custom sql thì có thể sử thêm whereRaw vào hàm ``mergeQuery()`` trong repository như sau:
  ```php
    // UserService.php
    public function list($conditions, $relations = [], $relationCounts = []) {
        $conditions['raw'] = "`id` != 1";
        return $this->repository->list($conditions, $relations = [], $relationCounts = []);
    }

    // UserRepository.php
    public function mergeQuery($query, $column, $data)
    {
        switch ($column) {
            case 'type':
                return $query->where($column, $data);
            case 'name':
                return $query->whereRaw("concat(last_name, '', first_name) like '%$data%' ");
            case 'email':
            case 'username':
                return $query->where($column, 'like', '%' . $data . '%');
                break;
            case 'raw':
                return $query->whereRaw($data);
                break;
            default:
                return $query;
                break;
        }
    }
  ```

- Relation:

    TH1:

        $this->repository->list($conditions, ['roles'])

    TH2:

        $this->repository->list($conditions, ['roles' => function ($query) use($data) {
            $query->where('name', 'admin');
        }]);

    TH3: lấy relation của relation

        $this->repository->list($conditions, ['roles', 'menus.menus']);

    TH4: lấy relation của relation and custom
    
        $this->repository->list($conditions, ['roles', 'menus.menus' => function ($query) use($data) {
            $query->where('name', 'user.index');
        }]);

  > `$this->repository->findByCondition($conditions, $relations, $relationCounts)` sử dụng để lấy danh theo condition nhưng ko trả về pagination mà trả về 1 collection.
    - `$conditions`: mảng các điều kiện.
    - `$relation`: mảng realtion cần trả về.
    - `$relationCounts`: mảng realtion cần trả về số lượng.

  > `$this->repository->create($data)` sử dụng để tạo record.
   - `$data`: mảng dữ liệu cần tạo.
   `$this->repository->detail($user, $relations)`
      `$user`: là instance of model cần update.
      `$relation`: mảng realtion cần trả về.

  > `$this->repository->update($user, $data)` sử dụng dể update một record.
   - `$user`: là instance of model cần update.
   - `$data`: là mảng dữ các trường và dự liêu cần update

  > `$this->repository->delete($user)` Để xóa 1 record.
   - `$user`: là instance of model cần xóa.

## Coding convention
- Quy tắc đặt tên
    
  - (camelCase) `ký tự đầu tiên của từ đầu tiên viết thường những ký tự đầu tiên của những từ tiếp theo được viết hoa --> áp dụng cho: tên biến, tên hàm`
  - (PascalCase) `cú pháp Pascal viết hoa chữ cái đầu tiên của mỗi từ --> áp dụng cho: tên lớp`
  - (snake_case) `cú pháp con rắn, tất cả các chữ cái đều viết thường, và các từ cách nhau bởi dấu gạch dưới --> áp dụng cho: tên biến, tên hàm`
  

- Quy tắc số lượng

    - `Hàm không nên quá 30 dòng`
    - `Lớp không nên vượt quá 500 dòng`
    - `Một hàm không được vượt quá 5 tham số, nên giữ <=3`
    - `Một hàm chỉ nên làm duy nhất 1 việc`
    - `Khi khai báo biến, một dòng chỉ chứa một biến`
    - `Một dòng không nên dài quá 80 ký tự`
    - `Các câu lệnh lồng nhau tối đa 4 cấp`


- Quy tắc xuống dòng

    - `Nếu có dấu phẩy thì xuống hàng sau dấu phẩy`
    - `Nếu có nhiều cấp lồng nhau, thì xuống hàng theo từng cấp`
    - `Dòng xuống hàng mới thì nên bắt đầu ở cùng cột với đoạn lệnh cùng cấp ở trên`


- Quy tắc tại function
    - `Các function comment cần có đầy đủ các thành phần: Mô tả chức năng, biến, dạng trả về`
    - `Các function nên được khai báo "type hint" và "return type" đẩy đủ` 

## Language

    php artisan lang:generate resources/lang/labels.xlsx
    php artisan lang:generate resources/lang/messages.xlsx

Tạo key google đọc tại đây: [Link](./packages/languages/README.md)

## Role/Permission

**Thêm quyền**

- B1: Thêm quyền tại config('permission.data') với cấu trúc có sẵn
- B2: Chạy: `php artisan db:seed --class=PermissionSeeder`

## Table

Tham khảo tại: [Link](./resources/views/admin/modules/demo/index.blade.php) hoặc các Module có sẵn. VD: User, Article,...

## Form và Validation

Tham khảo tại: [Link](./resources/views/admin/modules/demo/create.blade.php) hoặc các Module có sẵn. VD: User, Article,...

## Mail Template

Thêm loại Mail tại Model [App\Models\MailTemplate::class](./app/Models/MailTemplate.php)

Ví dụ gửi Mail:

    $email = 'test@relipasoft.com';
    SendMailTemplateJob::dispatch(MailTemplate::MAIL_TEMPLATES_PASSWORD_RESET, $email, [
        'link_url' => '',
    ]);

## Export

    $dataExport = [
        'file_name' => '',
        'fields' => [],
        'column_widths' => [],
        'column_formats' => [],
        'data' => [],
    ];

    // CSV
    return Excel::download(new ExportGeneral($dataExport), $dataExport['file_name'] . '.csv');

    // XLSX
    ob_end_clean();
    ob_start();
    return Excel::download(new ExportGeneral($dataExport), $dataExport['file_name'] . '.xlsx');

Tham khảo thêm tại:
[App\Http\Controllers\Admin\ContactController@export](./app/Http/Controllers/Admin/ContactController.php)

## DummyData

    php artisan migrate
    php artisan db:seed
    php artisan transe:seed:product_v2
    php artisan db:seed --class=DataDummySeeder
