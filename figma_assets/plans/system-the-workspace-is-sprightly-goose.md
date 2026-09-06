# OnTap — E2E Educational & Online Examination Platform
## Implementation Plan

### Context

The user wants a full-stack Laravel + Livewire 3 + Alpine.js + Tailwind CSS UI for "OnTap", a comprehensive exam platform with Student, Teacher, and Admin portals. The Figma Make environment is React + Vite — this plan builds a high-fidelity React prototype that faithfully mirrors the exact HTML structure, Tailwind classes, and Livewire/Alpine.js attribute patterns that the Blade templates would use. Every component maps 1:1 to a future Blade equivalent; comments in JSX mark `wire:` and `x-` hooks at their exact positions.

The user's explicit aesthetic overrides `create_make_theme` suggestions: **Inter font, Indigo-600 (`#4F46E5`) / Blue-500 (`#3B82F6`) primary, Slate neutrals, Emerald/Rose/Amber status colors, rounded-xl, shadow-sm/md, 8px grid**.

---

### Environment Note

Because Figma Make is a React + Vite environment, all screens are implemented as React components. The HTML structure, Tailwind classes, and placeholder `wire:` / `x-data` attributes embedded in JSX represent the exact markup that would ship inside `.blade.php` files. The result is a fully interactive clickable prototype usable as a design handoff for a Laravel developer.

---

### Aesthetic & Theme

| Token | Value |
|---|---|
| Font | Inter (Google Fonts) |
| Primary | Indigo-600 `#4F46E5` / Blue-500 `#3B82F6` |
| Background | Slate-50 (`#F8FAFC`) |
| Card | White |
| Text | Slate-900 / Slate-600 |
| Border | Slate-200 |
| Success | Emerald-500 |
| Error | Rose-500 |
| Warning | Amber-500 |
| Radius | `rounded-xl` (12px) |
| Shadow | `shadow-sm` cards, `shadow-md` modals/drawers |

---

### Dependencies to Install

- `react-router-dom` — multi-portal SPA routing
- `recharts` — radar chart and sparklines (History/Analytics, Admin MRR)
- `lucide-react` — icons throughout all portals

---

### File Structure

```
src/
  index.css                    ← add Google Fonts @import for Inter
  App.tsx                      ← BrowserRouter + all portal routes
  components/
    layouts/
      AppLayout.tsx            ← sidebar + responsive header + notification drawer
      GuestLayout.tsx          ← centered auth card layout
    ui/
      Badge.tsx                ← status badge (success/error/warning/neutral)
      KpiCard.tsx              ← metric card with icon
      DataTable.tsx            ← reusable table with pagination controls
      Modal.tsx                ← Alpine-style modal (React state)
      ConfirmModal.tsx         ← delete confirmation dialog
      SearchInput.tsx          ← debounced search field
  pages/
    auth/
      Login.tsx
      Register.tsx
      ForgotPassword.tsx
      Profile.tsx              ← avatar upload + dark/light toggle
    student/
      Dashboard.tsx            ← greeting + KPI cards + streak heatmap
      ExamSetup.tsx            ← cascading dropdowns + question count slider
      ExamRoom.tsx             ← focus layout + countdown timer + nav grid
      ExamResult.tsx           ← circular score badge + answer review
      SpacedRepetition.tsx     ← flip card + rating buttons
      History.tsx              ← radar chart + past exam log table
      Leaderboard.tsx          ← XP table + achievements + pricing tiers
    teacher/
      Dashboard.tsx            ← metrics + activity timeline
      QuestionManager.tsx      ← data table + search + filters + inline actions
      QuestionCreator.tsx      ← LaTeX preview form + multi-choice rows
      OcrUpload.tsx            ← drag-drop upload + split preview/edit view
      ExamBuilder.tsx          ← question bank filter + checkbox select + paper config
      Reports.tsx              ← class roster + difficulty analysis
    admin/
      Dashboard.tsx            ← system analytics + sparkline MRR card
      TaxonomyManager.tsx      ← collapsible nested tree + inline add modal
      UserManagement.tsx       ← data table + role switcher + ban toggle
      AuditLogs.tsx            ← paginated timestamped log stream
```

---

### Routing (App.tsx)

```
/login                     → Login
/register                  → Register
/forgot-password           → ForgotPassword
/profile                   → Profile

/student/dashboard         → Student Dashboard
/student/exam/setup        → ExamSetup
/student/exam/room         → ExamRoom
/student/exam/result       → ExamResult
/student/spaced-repetition → SpacedRepetition
/student/history           → History
/student/leaderboard       → Leaderboard

/teacher/dashboard         → Teacher Dashboard
/teacher/questions         → QuestionManager
/teacher/questions/create  → QuestionCreator
/teacher/ocr-upload        → OcrUpload
/teacher/exam-builder      → ExamBuilder
/teacher/reports           → Reports

/admin/dashboard           → Admin Dashboard
/admin/taxonomy            → TaxonomyManager
/admin/users               → UserManagement
/admin/audit-logs          → AuditLogs
```

Default route `/` redirects to `/student/dashboard`.

---

### Screen-by-Screen Implementation Notes

#### AppLayout
- Left sidebar: logo, portal-aware nav links grouped by section, collapse toggle
- Top header: breadcrumb, notification bell (slide-over drawer via state), user avatar dropdown
- Notification drawer: slides in from right, lists 5 mock alerts with timestamps
- Responsive: sidebar collapses to icon-only at `lg:` breakpoint

#### GuestLayout
- Full-height centered card, indigo gradient strip on left (desktop), OnTap logo

#### Auth Screens
- Login: email + password, "Remember me" checkbox, forgot password link
- Register: name, email, password, confirm password, role selector (Student/Teacher)
- ForgotPassword: email input + success state
- Profile: avatar upload dropzone, name/email fields, dark/light mode toggle (localStorage)

#### Student — Dashboard
- Greeting banner with current date
- 3 KPI cards: Total Exams, Average Score %, Accuracy Rate
- "Continue Learning" card list (3 items with progress bars)
- Weekly streak heatmap: 7-day grid with Indigo fill intensity

#### Student — ExamSetup (wire:model cascading dropdowns)
- Major → Subject → Sub-subject: each re-fetches options on change
- Question count: range slider (10–80, step 5)
- Time limit selector (15/30/45/60 min)
- "Start Exam" CTA button

#### Student — ExamRoom
- Sticky header: exam title, subject tag, live countdown (HH:MM:SS)
- Main panel: question number, question text, 4 radio card options (Tailwind radio group)
- "Flag for Review" button
- Right sidebar: 40-cell navigation grid — white (unanswered), indigo (answered), amber (flagged)
- Bottom: Previous / Next / Submit buttons
- Countdown implemented with `useEffect` + `setInterval`

#### Student — ExamResult
- SVG circular progress ring showing score percentage
- 4 stat pills: Correct (emerald), Wrong (rose), Skipped (slate), Time (blue)
- Scrollable answer review list: each question shows user answer vs correct answer with color coding

#### Student — SpacedRepetition
- Flip card: front shows question, back shows answer explanation
- CSS perspective transform for flip animation
- Three rating buttons: Hard (rose), Good (amber), Easy (emerald)
- Progress indicator: "Card 12 of 48"

#### Student — History
- Recharts RadarChart with 6 subject axes
- Past exam log table: Date, Subject, Score, Duration, Grade badge, View button
- Pagination controls

#### Student — Leaderboard
- Tabs: This Week / All Time
- XP ranking table with avatars, names, XP points, rank badges
- Achievements grid (12 badge slots, some locked/unlocked)
- Pricing table: 3 cards (Free, Pro 99k, VIP 199k) with feature comparison, CTA buttons
- Payment modal (Alpine-style: useState)

#### Teacher — Dashboard
- Metric cards: Uploaded Questions, Pending Reviews, OCR Queue
- Recent activity timeline with avatar + action description + timestamp

#### Teacher — QuestionManager
- Search input + category filter dropdown + difficulty filter (All/Easy/Medium/Hard)
- Data table: ID, Question preview, Subject, Difficulty tag, Created date, Actions
- Actions: Edit (pencil icon) + Delete (trash icon with confirm modal)
- Pagination

#### Teacher — QuestionCreator
- Question type toggle (MCQ / True-False)
- Rich text area for question (with LaTeX preview panel side-by-side)
- 4 option input rows with radio to mark correct answer
- Difficulty selector, Subject cascading dropdowns
- Save Draft / Publish buttons

#### Teacher — OcrUpload
- Drag-and-drop zone (dashed border, hover state)
- Left panel: uploaded image/PDF preview
- Right panel: editable textarea with extracted OCR text (mock)
- "Auto-parse to MCQs" button triggers a loading state → shows parsed question list

#### Teacher — ExamBuilder
- Question bank filter: Subject + Difficulty + Search
- Filterable question list with checkboxes
- Selected questions panel (right side)
- Config panel: Title, Time limit, Max attempts, Exam code display
- "Generate Exam" button

#### Teacher — Reports
- Class roster table with student names, scores, ranks
- Question difficulty analysis: table showing questions with failure rate > 50% highlighted in rose

#### Admin — Dashboard
- System stat cards: Total Users, Active Sessions, Server Load (with progress bar)
- MRR card with Recharts LineChart sparkline
- Monthly revenue trend

#### Admin — TaxonomyManager
- 3 root nodes (Major subjects) with expand/collapse toggle
- Each expands to Subjects, each Subject expands to Sub-subjects
- Each node has "+ Add Child" and "Edit" / "Delete" inline actions
- "Add Node" inline form with name input

#### Admin — UserManagement
- Search + role filter
- Data table: Avatar, Name, Email, Role (dropdown), Status (Active/Banned), Joined date, Actions
- Bulk action bar (appears when rows selected): Delete, Change Role, Ban
- Ban toggle switch per row

#### Admin — AuditLogs
- Filters: Date range + Action type
- Paginated log stream: timestamp, actor avatar+name, action description, target entity
- Color-coded action types: CREATE (emerald), UPDATE (blue), DELETE (rose), LOGIN (slate)

---

### Implementation Steps

1. Install dependencies: `react-router-dom`, `recharts`, `lucide-react`
2. Wire Inter font in `src/index.css` via Google Fonts `@import`
3. Add Tailwind CSS custom tokens in `src/index.css` (`@theme` block)
4. Build `AppLayout` and `GuestLayout` components
5. Build shared UI atoms: `Badge`, `KpiCard`, `DataTable`, `Modal`, `ConfirmModal`, `SearchInput`
6. Set up routing in `App.tsx` with all routes
7. Implement Auth screens (Login, Register, ForgotPassword, Profile)
8. Implement Student portal screens in order (Dashboard → ExamRoom → ExamResult → SpacedRepetition → ExamSetup → History → Leaderboard)
9. Implement Teacher portal screens (Dashboard → QuestionManager → QuestionCreator → OcrUpload → ExamBuilder → Reports)
10. Implement Admin portal screens (Dashboard → TaxonomyManager → UserManagement → AuditLogs)
11. Wire navigation links in AppLayout for all portals

---

### Verification

- Navigate every route; confirm no blank pages or console errors
- ExamRoom: countdown ticks down in real time
- SpacedRepetition: card flip animation works
- TaxonomyManager: nodes expand and collapse
- QuestionManager: confirm modal opens/closes on delete
- Notification drawer: opens from bell icon, dismisses on overlay click
- Responsive: sidebar collapses at narrow viewport, tables scroll horizontally on mobile
