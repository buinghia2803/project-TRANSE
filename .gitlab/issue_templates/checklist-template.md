### Transe

### [Developer]
- [ ] Đã pull source code mới nhất từ nhánh develop.
- [ ] Đã xác nhận source code không có các thay đổi không cần thiết, như là:
  - Khoảng trắng thừa.
  - File thừa hoặc thay đổi trong source code không liên quan đến task.

- [ ] Đặt tên title merge request và commit message theo định dạng `[prefix]([feature|screen]): [description]`
  - [prefix]: •	feat      : thêm một feature.
              •	fix       : fix bug cho hệ thống.
              •	refactor  : sửa code nhưng không fix bug cũng không thêm feature hoặc đôi khi bug cũng được fix từ việc refactor.
              •	docs      : thêm/thay đổi document.
              •	chore     : những sửa đổi nhỏ nhặt không liên quan tới code.
              •	style     : những thay đổi không làm thay đổi ý nghĩa của code như thay đổi css/ui chẳng hạn.
              •	perf      : code cải tiến về mặt hiệu năng xử lý.
              •	vendor    : cập nhật version cho các dependencies, packages.
  - [feature|screen]: Chức năng hoặc màn hình nằm trong scope.
  - [description]: Mô tả nội dung của task.
  Ex: feat(user): create api create/update for user.

- [ ] Source code đã tuân theo chuẩn coding conventions.
- [ ] Đã selftest và đảm bảo 100% theo checklist.
- [ ] Đã ghi đầy đủ, rõ ràng các lệnh cần chạy vào Merge Request.

      `Các lệnh cần chạy : `

- [ ] Đã tô màu spec, chụp lại hình ảnh tô màu spec và đưa vào Merge Request.
- [ ] Đã selftest lại và xác nhận KHÔNG ẢNH HƯỞNG TỚI CHỨC NĂNG KHÁC.
- [ ] Đã đổi Backlog task sang trạng thái Review.
- [ ] Đã review lại code đảm bảo không có comment thừa, console, print, dump die(dd).

### [Tester]

- [ ] Đã đọc và kiểm tra kết quả selftest của developer.
- [ ] Đã test lại và xác nhận KHÔNG GÂY ẢNH HƯỞNG TỚI CHỨC NĂNG KHÁC.
- [ ] Đã chuyển Backlog task sang trạng thái Test Done(Đã fix hết bug)

### [Reviewer]

- [ ] Xác nhận code chuẩn coding conventions.
- [ ] Xác nhận code không có vấn đề gì.

