# Quy ước phát triển CARE IOC

Hệ thống phục vụ **Điều hành thông minh cho Bệnh viện Đa khoa Khu vực Đăk Tô** và quản trị dữ liệu qua ba khu vực/trang: `admin`, `backend`, `dashboard`. Áp dụng các quy ước này cho mọi hạng mục mới hoặc thay đổi liên quan.

## Giao diện

- Thiết kế responsive cho điện thoại, máy tính bảng và máy tính; ưu tiên luồng thao tác rõ ràng, ít bước và dễ sử dụng trong môi trường bệnh viện.
- Form phải kiểm tra dữ liệu cả phía trình duyệt lẫn máy chủ: trường bắt buộc, định dạng, miền giá trị và dữ liệu tham chiếu trước khi lưu.
- Sau mọi tác vụ (thêm, sửa, xóa, import, gọi API), hiển thị thông báo nhỏ, dễ đọc ở góc phải phía dưới màn hình về thành công hoặc thất bại. Dùng SweetAlert (`Swal.fire`) theo dạng toast với vị trí `bottom-end`; không sử dụng hộp thoại `alert()` cho thông báo nghiệp vụ thông thường.
- Thông báo lỗi phải nêu nguyên nhân có thể xử lý; không trả về lỗi kỹ thuật hoặc thông tin nhạy cảm cho người dùng.

## Luồng frontend và backend

- Điều hướng trang, lấy dữ liệu hiển thị ban đầu và xử lý logic trang đặt tại `indexController`.
- Toàn bộ frontend quản trị chỉ được khởi tạo và chỉnh sửa trong thư mục `template/admin`; không tạo hoặc sửa giao diện quản trị tại thư mục template khác.
- Mỗi action hiển thị trang quản trị tại `indexController` gọi `$this->view->admintmp('tên-file-view')`, trong đó view tương ứng nằm tại `template/admin`.
- Tác vụ thay đổi dữ liệu từ frontend sử dụng AJAX bằng jQuery gửi đến `apiController`; API phản hồi JSON nhất quán, tối thiểu gồm `success`, `message` và `data` khi cần.
- Frontend chịu trách nhiệm hiển thị, bắt sự kiện và khai báo jQuery AJAX; `apiController` chịu trách nhiệm xác thực, query database và xử lý nghiệp vụ rồi trả kết quả JSON cho frontend.
- API phải xác thực yêu cầu, kiểm tra CSRF/quyền truy cập theo cơ chế hiện có, validate lại toàn bộ dữ liệu ở máy chủ và chỉ trả về dữ liệu cần thiết.
- Frontend cập nhật bảng/danh sách sau phản hồi AJAX, đồng thời dùng SweetAlert (`Swal.fire`) để hiển thị thông báo thành công/thất bại; không dùng `alert()` cho thông báo nghiệp vụ.
- Ưu tiên code sạch, trực tiếp và dễ đọc; chỉ tách hàm khi có trách nhiệm rõ ràng, tránh gọi hoặc tạo quá nhiều hàm làm rối luồng xử lý.

## Dữ liệu và tích hợp

- Được phép truy cập ở chế độ đọc để xem cấu trúc và dữ liệu các bảng trong database `care_ioc` tại phpMyAdmin: `http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=care_ioc`.
- Không tự ý tạo thêm table trong database. Khi thấy cần table mới, phải trình bày rõ mục đích, tên table và danh sách đầy đủ các cột (tên cột, kiểu dữ liệu, ràng buộc/khóa và ý nghĩa) để người dùng xem xét.
- Chỉ được thực hiện lệnh tạo table sau khi người dùng cho phép rõ ràng. Việc được phép đọc database không đồng nghĩa với quyền thay đổi cấu trúc hoặc dữ liệu.
- Hệ thống hỗ trợ hai nguồn nhập dữ liệu: nhập thủ công có kiểm soát và đồng bộ/call API từ các phần mềm LIS, RIS, EMR.
- Thiết kế bảng dữ liệu cần lưu được nguồn dữ liệu, thời điểm đồng bộ/nhập, trạng thái xử lý và khóa định danh để chống trùng lặp khi đồng bộ.
- Không ghi đè dữ liệu tích hợp bằng dữ liệu thủ công mà không có quy tắc nghiệp vụ rõ ràng; lưu dấu vết thay đổi khi hạng mục có yêu cầu kiểm toán.
- Tích hợp ngoài cần có xử lý lỗi, timeout, ghi log và cơ chế chạy lại an toàn.

## Quy trình nhận công việc từ Google Sheet

- Google Sheet theo dõi công việc: `Bảng tổng hợp công việc nhóm - dev` (tab `Công việc`, kèm `Danh mục` và `Dashboard`).
- Khi người dùng gửi đúng lệnh `procces in google sheet`, đọc mã việc và mô tả chi tiết đã được cung cấp trong Sheet, sau đó đối chiếu với mã nguồn hiện tại trước khi thay đổi.
- Triển khai giao diện quản trị trong `template/admin`; điều hướng và logic hiển thị đặt tại `indexController`, gọi view bằng `$this->view->admintmp('tên-file-view')`; triển khai tác vụ AJAX, kiểm tra dữ liệu và phản hồi JSON trong `apiController`.
- Sau khi thực hiện, kiểm tra cú pháp và luồng liên quan, rồi báo cáo chính xác các tệp/chức năng đã thay đổi.
