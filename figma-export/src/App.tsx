import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import AppLayout from "./components/layouts/AppLayout";
import GuestLayout from "./components/layouts/GuestLayout";

// Auth
import Login from "./pages/auth/Login";
import Register from "./pages/auth/Register";
import ForgotPassword from "./pages/auth/ForgotPassword";
import Profile from "./pages/auth/Profile";

// Student
import StudentDashboard from "./pages/student/Dashboard";
import ExamSetup from "./pages/student/ExamSetup";
import ExamRoom from "./pages/student/ExamRoom";
import ExamResult from "./pages/student/ExamResult";
import SpacedRepetition from "./pages/student/SpacedRepetition";
import History from "./pages/student/History";
import Leaderboard from "./pages/student/Leaderboard";

// Teacher
import TeacherDashboard from "./pages/teacher/Dashboard";
import QuestionManager from "./pages/teacher/QuestionManager";
import QuestionCreator from "./pages/teacher/QuestionCreator";
import OcrUpload from "./pages/teacher/OcrUpload";
import ExamBuilder from "./pages/teacher/ExamBuilder";
import Reports from "./pages/teacher/Reports";

// Admin
import AdminDashboard from "./pages/admin/Dashboard";
import TaxonomyManager from "./pages/admin/TaxonomyManager";
import UserManagement from "./pages/admin/UserManagement";
import AuditLogs from "./pages/admin/AuditLogs";

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        {/* Default redirect */}
        <Route path="/" element={<Navigate to="/student/dashboard" replace />} />

        {/* Auth routes */}
        <Route element={<GuestLayout />}>
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/forgot-password" element={<ForgotPassword />} />
        </Route>

        {/* App routes (with sidebar) */}
        <Route element={<AppLayout />}>
          <Route path="/profile" element={<Profile />} />

          {/* Student */}
          <Route path="/student/dashboard" element={<StudentDashboard />} />
          <Route path="/student/exam/setup" element={<ExamSetup />} />
          <Route path="/student/exam/room" element={<ExamRoom />} />
          <Route path="/student/exam/result" element={<ExamResult />} />
          <Route path="/student/spaced-repetition" element={<SpacedRepetition />} />
          <Route path="/student/history" element={<History />} />
          <Route path="/student/leaderboard" element={<Leaderboard />} />

          {/* Teacher */}
          <Route path="/teacher/dashboard" element={<TeacherDashboard />} />
          <Route path="/teacher/questions" element={<QuestionManager />} />
          <Route path="/teacher/questions/create" element={<QuestionCreator />} />
          <Route path="/teacher/ocr-upload" element={<OcrUpload />} />
          <Route path="/teacher/exam-builder" element={<ExamBuilder />} />
          <Route path="/teacher/reports" element={<Reports />} />

          {/* Admin */}
          <Route path="/admin/dashboard" element={<AdminDashboard />} />
          <Route path="/admin/taxonomy" element={<TaxonomyManager />} />
          <Route path="/admin/users" element={<UserManagement />} />
          <Route path="/admin/audit-logs" element={<AuditLogs />} />
        </Route>

        {/* Fallback */}
        <Route path="*" element={<Navigate to="/student/dashboard" replace />} />
      </Routes>
    </BrowserRouter>
  );
}
