# 🔍 Phân Tích Dự Án OnTap & Gợi Ý Phát Triển

## Tổng Quan Hiện Tại

**OnTap** là nền tảng ôn thi trắc nghiệm trực tuyến, xây dựng bằng **Laravel + Livewire**, tích hợp **Gemini AI**. Hệ thống có 3 role: **Student**, **Teacher**, **Admin**.

### ✅ Những gì đã hoàn thành tốt

| Module | Tính năng | Trạng thái |
|--------|-----------|------------|
| **OCR + AI** | Upload PDF/Ảnh → trích xuất câu hỏi tự động (Gemini Vision) | ✅ Hoàn chỉnh |
| **AI Generate** | Tự soạn câu hỏi từ tài liệu (phân bổ dễ/TB/khó) | ✅ Hoàn chỉnh |
| **Explainable AI** | Giải thích tại sao đáp án sai/đúng | ✅ Hoàn chỉnh |
| **Adaptive AI** | Sinh biến thể câu hỏi khi sai ≥ 3 lần (Job) | ✅ Hoàn chỉnh |
| **Thi cử** | Tạo đề → Phòng thi → Chấm điểm tự động | ✅ Hoàn chỉnh |
| **Ma trận đề** | 3 chế độ: Ngẫu nhiên / Theo độ khó / Tỷ lệ tùy chỉnh | ✅ Hoàn chỉnh |
| **Progress Tracking** | Radar chart, mistake heatmap, thống kê câu sai | ✅ Hoàn chỉnh |
| **Teacher - CRUD** | Quản lý câu hỏi, duyệt câu, tạo đề | ✅ Hoàn chỉnh |
| **Admin** | Quản lý taxonomy (Ngành→Môn→Chương), User, Audit Logs | ✅ Hoàn chỉnh |
| **Auth & RBAC** | Spatie Permission (student/teacher/super_admin) | ✅ Hoàn chỉnh |

---

## ⚠️ Những phần đang dùng dữ liệu giả (TODO)

| Module | Vấn đề |
|--------|--------|
| **Spaced Repetition** ([SpacedRepetition.php](file:///d:/laragon/www/OnTap/app/Livewire/Student/SpacedRepetition.php)) | Đang dùng `$deck` hardcoded 3 câu, chưa kết nối với `ProgressTrackingService::layHangDoiOnTap()` |
| **Leaderboard** ([Leaderboard.php](file:///d:/laragon/www/OnTap/app/Livewire/Student/Leaderboard.php)) | Dữ liệu ranking hardcoded, chưa query thực từ DB |
| **Teacher Reports** ([Reports.php](file:///d:/laragon/www/OnTap/app/Livewire/Teacher/Reports.php)) | Roster & difficulty analysis dùng array giả |
| **Streak Heatmap** ([Dashboard.php](file:///d:/laragon/www/OnTap/app/Livewire/Student/Dashboard.php#L56-L59)) | `streakData` đang dùng `rand()` |
| **Payment** ([Leaderboard.php](file:///d:/laragon/www/OnTap/app/Livewire/Student/Leaderboard.php#L58-L62)) | `upgradePlan()` chỉ flash message, chưa tích hợp payment gateway |

---

## 🚀 Gợi Ý Phát Triển Thêm (Xếp theo mức ưu tiên)

### 🔴 Ưu Tiên Cao — Hoàn thiện tính năng đang dở

#### 1. Hoàn thiện Spaced Repetition (Ôn Tập Lặp Lại)
> Đây là tính năng cốt lõi nhất của app ("OnTap" = Ôn tập) nhưng đang dùng data giả.

- Kết nối `SpacedRepetition` component với `ProgressTrackingService::layHangDoiOnTap()`
- Implement thuật toán SM-2 (SuperMemo) hoặc Leitner Box thực sự
- Thêm model `FlashCard` / `SpacedRepetitionSchedule` để lưu lịch ôn tập
- Tính `interval`, `ease_factor`, `next_review_date` cho mỗi câu hỏi
- Push notification / email nhắc ôn tập

#### 2. Hoàn thiện Leaderboard thực
- Query `ExamAttempt` để tính điểm thực
- Ranking theo tuần / tháng / tất cả
- XP system: +10 điểm/câu đúng, +50 điểm streak, +100 điểm hoàn thành đề
- Hiển thị rank thật của user đang đăng nhập

#### 3. Hoàn thiện Teacher Reports thực
- Query `ExamAttempt` + `UserQuestionStat` để lấy dữ liệu thực
- Câu hỏi có tỷ lệ sai cao nhất (Item Analysis)
- Phân bố điểm theo lớp/nhóm
- Export báo cáo ra PDF/Excel

#### 4. Streak Heatmap thực
- Track ngày nào user có làm bài (từ `luot_thi.bat_dau_luc`)
- Hiển thị heatmap kiểu GitHub contributions

---

### 🟡 Ưu Tiên Trung Bình — Tính năng mới có giá trị cao

#### 5. 📱 Chế Độ Thi Thử (Mock Exam / Simulation)
- Giả lập điều kiện thi thật: đếm ngược, không quay lại câu trước
- Anti-cheat: phát hiện chuyển tab, fullscreen mode
- Có sẵn cột `tab_switches`, `is_fullscreen` trong migration nhưng chưa dùng

#### 6. 🏫 Quản Lý Lớp Học (Class Management)
- Teacher tạo lớp, mời sinh viên bằng mã lớp
- Giao bài thi cho lớp, theo dõi tiến độ cả lớp
- So sánh điểm giữa các lớp
- Model: `Classroom`, `ClassroomStudent`, `Assignment`

#### 7. 💬 Thảo Luận Câu Hỏi (Discussion / Comments)
- Student bình luận / hỏi đáp dưới mỗi câu hỏi sau khi thi
- Teacher trả lời, đánh dấu "câu hỏi hay"
- Model: `QuestionComment`

#### 8. 📊 Dashboard Analytics Nâng Cao cho Student
- Biểu đồ tiến bộ theo thời gian (line chart điểm trung bình qua các tuần)
- So sánh với trung bình lớp (nếu có Class Management)
- Gợi ý chương yếu cần ôn tập (dựa trên `UserSubSubjectProgress`)
- AI recommendation: "Bạn nên ôn lại Chương 3 — độ thành thạo chỉ 35%"

#### 9. 🔔 Hệ Thống Thông Báo (Notification System)
- Thông báo khi: teacher duyệt/từ chối câu hỏi, có bài thi mới, nhắc ôn tập
- Sử dụng Laravel Notifications + database driver
- Dropdown bell icon trên navigation
- Model: `notifications` table (Laravel built-in)

#### 10. 📤 Xuất Đề Thi Ra PDF / Word
- Teacher tạo đề thi xong → export ra PDF đẹp (có header trường, mã đề)
- Dùng DomPDF hoặc Snappy
- Tạo nhiều mã đề (xáo trộn thứ tự câu hỏi / đáp án)
- Export kèm đáp án riêng cho giáo viên

---

### 🟢 Ưu Tiên Thấp — "Nice to have"

#### 11. 💳 Tích Hợp Thanh Toán (VNPay / MoMo / Stripe)
- Đã có UI gói Free/Pro/Team trong Leaderboard
- Implement subscription logic: giới hạn số lần thi/tháng cho Free
- Gói Pro: unlimited + spaced repetition + AI hints

#### 12. 🤖 AI Chatbot Trợ Giảng
- Chatbox nhỏ giúp student hỏi kiến thức bất cứ lúc nào
- Dùng Gemini API đã có, thêm context về môn học đang ôn
- Lưu lịch sử chat

#### 13. 🏅 Gamification Nâng Cao
- Huy hiệu (Badges): Streak 7 ngày, Điểm 10, Hoàn thành 100 câu...
- Level system: Newbie → Learner → Scholar → Master
- Daily challenges: 5 câu/ngày để giữ streak

#### 14. 📱 PWA (Progressive Web App)
- Thêm manifest.json + service worker
- Offline mode: cache câu hỏi đã tải → ôn tập khi mất mạng
- Push notification trên điện thoại

#### 15. 🔗 Chia Sẻ & Social
- Chia sẻ kết quả thi lên Facebook/Zalo (ảnh card đẹp)
- Tạo link mời bạn cùng thi
- Study groups

#### 16. 📝 Import/Export Câu Hỏi Hàng Loạt
- Import từ Excel/CSV (ngoài OCR từ PDF đã có)
- Export ngân hàng câu hỏi ra Excel
- Chuẩn GIFT format (tương thích Moodle)

---

## 🛠️ Cải Tiến Kỹ Thuật (Technical Debt)

| Mục | Chi tiết |
|-----|----------|
| **Testing** | Chưa có test nào thật sự (`.phpunit.result.cache` trống) — cần viết Feature/Unit tests |
| **API Layer** | Chưa có REST API — nếu muốn làm mobile app sau này cần thêm `api.php` routes |
| **Caching** | Chỉ cache auto-save bài thi, chưa cache query nặng (leaderboard, reports) |
| **Queue** | Đã setup Jobs nhưng cần verify queue worker chạy ổn (AI jobs) |
| **Rate Limiting** | Gemini API có multi-key rotation, nhưng chưa có rate limit ở application level |
| **Localization** | Code mix Việt/Anh, có thể chuẩn hóa qua Laravel lang files |

---

## 📋 Gợi Ý Roadmap

```
Phase 1 (Hoàn thiện):  #1 Spaced Repetition + #2 Leaderboard + #3 Reports + #4 Streak
Phase 2 (Mở rộng):     #5 Mock Exam + #6 Class Management + #9 Notifications
Phase 3 (Nâng cao):    #8 Analytics + #7 Discussion + #10 Export PDF
Phase 4 (Monetize):    #11 Payment + #13 Gamification + #14 PWA
```
