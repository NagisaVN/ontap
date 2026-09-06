import { useState } from "react";
import { NavLink, Outlet, useLocation } from "react-router-dom";
import {
  LayoutDashboard, BookOpen, ClipboardList, BarChart2, Trophy,
  Users, FileQuestion, Upload, Wrench, FileText, Shield,
  TreePine, ScrollText, Bell, ChevronLeft, ChevronRight,
  X, User, LogOut, Settings, Sun, Moon, GraduationCap,
  Repeat, PlusCircle, BookMarked
} from "lucide-react";

interface NavItem {
  label: string;
  href: string;
  icon: React.ReactNode;
}

const studentNav: NavItem[] = [
  { label: "Dashboard", href: "/student/dashboard", icon: <LayoutDashboard size={18} /> },
  { label: "Start Exam", href: "/student/exam/setup", icon: <ClipboardList size={18} /> },
  { label: "Spaced Repetition", href: "/student/spaced-repetition", icon: <Repeat size={18} /> },
  { label: "History", href: "/student/history", icon: <BarChart2 size={18} /> },
  { label: "Leaderboard", href: "/student/leaderboard", icon: <Trophy size={18} /> },
];

const teacherNav: NavItem[] = [
  { label: "Dashboard", href: "/teacher/dashboard", icon: <LayoutDashboard size={18} /> },
  { label: "Questions", href: "/teacher/questions", icon: <FileQuestion size={18} /> },
  { label: "Add Question", href: "/teacher/questions/create", icon: <PlusCircle size={18} /> },
  { label: "OCR Upload", href: "/teacher/ocr-upload", icon: <Upload size={18} /> },
  { label: "Exam Builder", href: "/teacher/exam-builder", icon: <Wrench size={18} /> },
  { label: "Reports", href: "/teacher/reports", icon: <FileText size={18} /> },
];

const adminNav: NavItem[] = [
  { label: "Dashboard", href: "/admin/dashboard", icon: <LayoutDashboard size={18} /> },
  { label: "Taxonomy", href: "/admin/taxonomy", icon: <TreePine size={18} /> },
  { label: "Users", href: "/admin/users", icon: <Users size={18} /> },
  { label: "Audit Logs", href: "/admin/audit-logs", icon: <ScrollText size={18} /> },
];

const mockNotifications = [
  { id: 1, type: "exam", title: "Exam reminder", body: "Mathematics – Chapter 5 starts in 30 minutes.", time: "2m ago", read: false },
  { id: 2, type: "study", title: "Study streak", body: "You're on a 7-day streak! Keep it up.", time: "1h ago", read: false },
  { id: 3, type: "system", title: "New content available", body: "50 new questions added to Physics – Optics.", time: "3h ago", read: true },
  { id: 4, type: "exam", title: "Result published", body: "Your Chemistry exam result is ready to view.", time: "Yesterday", read: true },
  { id: 5, type: "system", title: "Subscription renewed", body: "Your Pro plan has been renewed successfully.", time: "2d ago", read: true },
];

export default function AppLayout() {
  const [collapsed, setCollapsed] = useState(false);
  const [notifOpen, setNotifOpen] = useState(false);
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const [dark, setDark] = useState(false);
  const location = useLocation();

  const portal = location.pathname.startsWith("/teacher")
    ? "teacher"
    : location.pathname.startsWith("/admin")
    ? "admin"
    : "student";

  const navItems = portal === "teacher" ? teacherNav : portal === "admin" ? adminNav : studentNav;

  const portalLabel = portal === "teacher" ? "Teacher Portal" : portal === "admin" ? "Admin Portal" : "Student Portal";
  const portalColor = portal === "teacher" ? "text-emerald-600" : portal === "admin" ? "text-rose-600" : "text-indigo-600";
  const portalBg = portal === "teacher" ? "bg-emerald-50 border-emerald-200" : portal === "admin" ? "bg-rose-50 border-rose-200" : "bg-indigo-50 border-indigo-200";

  const unreadCount = mockNotifications.filter(n => !n.read).length;

  return (
    <div className="flex h-full bg-slate-50 font-sans">
      {/* Sidebar */}
      <aside
        className={`flex flex-col border-r border-slate-200 bg-white transition-all duration-300 ${
          collapsed ? "w-16" : "w-60"
        } shrink-0`}
      >
        {/* Logo */}
        <div className={`flex items-center h-16 border-b border-slate-100 ${collapsed ? "justify-center px-0" : "px-5"}`}>
          <div className="flex items-center gap-2 min-w-0">
            <div className="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center shrink-0">
              <GraduationCap size={16} className="text-white" />
            </div>
            {!collapsed && (
              <span className="font-bold text-slate-900 text-lg tracking-tight">OnTap</span>
            )}
          </div>
        </div>

        {/* Portal badge */}
        {!collapsed && (
          <div className={`mx-3 mt-3 mb-1 px-3 py-1.5 rounded-lg border text-xs font-semibold ${portalColor} ${portalBg}`}>
            {portalLabel}
          </div>
        )}

        {/* Nav */}
        <nav className="flex-1 overflow-y-auto py-2 px-2">
          {navItems.map((item) => (
            <NavLink
              key={item.href}
              to={item.href}
              className={({ isActive }) =>
                `flex items-center gap-3 px-3 py-2.5 rounded-lg mb-0.5 text-sm font-medium transition-colors ${
                  isActive
                    ? "bg-indigo-50 text-indigo-700"
                    : "text-slate-600 hover:bg-slate-50 hover:text-slate-900"
                } ${collapsed ? "justify-center" : ""}`
              }
              title={collapsed ? item.label : undefined}
            >
              <span className="shrink-0">{item.icon}</span>
              {!collapsed && <span className="truncate">{item.label}</span>}
            </NavLink>
          ))}
        </nav>

        {/* Portal switcher */}
        {!collapsed && (
          <div className="p-3 border-t border-slate-100">
            <p className="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2 px-1">Switch Portal</p>
            <div className="flex flex-col gap-1">
              {[
                { label: "Student", href: "/student/dashboard", color: "text-indigo-600 hover:bg-indigo-50" },
                { label: "Teacher", href: "/teacher/dashboard", color: "text-emerald-600 hover:bg-emerald-50" },
                { label: "Admin", href: "/admin/dashboard", color: "text-rose-600 hover:bg-rose-50" },
              ].map((p) => (
                <NavLink
                  key={p.href}
                  to={p.href}
                  className={`flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors ${p.color}`}
                >
                  <BookMarked size={12} />
                  {p.label}
                </NavLink>
              ))}
            </div>
          </div>
        )}

        {/* Collapse button */}
        <div className="p-3 border-t border-slate-100">
          <button
            onClick={() => setCollapsed(!collapsed)}
            className="w-full flex items-center justify-center h-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
          >
            {collapsed ? <ChevronRight size={16} /> : <ChevronLeft size={16} />}
          </button>
        </div>
      </aside>

      {/* Main area */}
      <div className="flex flex-col flex-1 min-w-0">
        {/* Header */}
        <header className="h-16 border-b border-slate-200 bg-white flex items-center justify-between px-6 shrink-0">
          <div className="flex items-center gap-2 text-sm text-slate-500">
            <span className="font-semibold text-slate-900 capitalize">{portalLabel}</span>
          </div>
          <div className="flex items-center gap-2">
            {/* Dark mode */}
            <button
              onClick={() => setDark(!dark)}
              className="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition-colors"
            >
              {dark ? <Sun size={16} /> : <Moon size={16} />}
            </button>

            {/* Notifications */}
            <button
              onClick={() => setNotifOpen(true)}
              className="w-9 h-9 relative flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition-colors"
            >
              <Bell size={16} />
              {unreadCount > 0 && (
                <span className="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500" />
              )}
            </button>

            {/* Avatar */}
            <div className="relative">
              <button
                onClick={() => setUserMenuOpen(!userMenuOpen)}
                className="flex items-center gap-2 pl-2 pr-3 h-9 rounded-lg hover:bg-slate-100 transition-colors"
              >
                <div className="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center">
                  <span className="text-xs font-bold text-indigo-700">NG</span>
                </div>
                <span className="text-sm font-medium text-slate-700 hidden sm:block">Nguyen Giang</span>
              </button>
              {userMenuOpen && (
                <div className="absolute right-0 top-11 w-48 bg-white border border-slate-200 rounded-xl shadow-md z-50 py-1">
                  <NavLink to="/profile" className="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50" onClick={() => setUserMenuOpen(false)}>
                    <User size={14} /> Profile
                  </NavLink>
                  <NavLink to="/profile" className="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50" onClick={() => setUserMenuOpen(false)}>
                    <Settings size={14} /> Settings
                  </NavLink>
                  <hr className="my-1 border-slate-100" />
                  <NavLink to="/login" className="flex items-center gap-2.5 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50" onClick={() => setUserMenuOpen(false)}>
                    <LogOut size={14} /> Sign out
                  </NavLink>
                </div>
              )}
            </div>
          </div>
        </header>

        {/* Page content */}
        <main className="flex-1 overflow-y-auto">
          <Outlet />
        </main>
      </div>

      {/* Notification Drawer overlay */}
      {notifOpen && (
        <div
          className="fixed inset-0 bg-slate-900/30 z-40"
          onClick={() => setNotifOpen(false)}
        />
      )}

      {/* Notification Drawer */}
      <div
        className={`fixed top-0 right-0 h-full w-80 bg-white border-l border-slate-200 shadow-md z-50 flex flex-col transition-transform duration-300 ${
          notifOpen ? "translate-x-0" : "translate-x-full"
        }`}
      >
        <div className="flex items-center justify-between px-5 h-16 border-b border-slate-100">
          <h2 className="font-semibold text-slate-900">Notifications</h2>
          <button
            onClick={() => setNotifOpen(false)}
            className="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100"
          >
            <X size={16} />
          </button>
        </div>
        <div className="flex-1 overflow-y-auto divide-y divide-slate-100">
          {mockNotifications.map((n) => (
            <div key={n.id} className={`px-5 py-4 ${!n.read ? "bg-indigo-50/50" : ""}`}>
              <div className="flex items-start gap-3">
                <div className={`w-2 h-2 rounded-full mt-1.5 shrink-0 ${!n.read ? "bg-indigo-500" : "bg-slate-200"}`} />
                <div className="min-w-0">
                  <p className="text-sm font-semibold text-slate-900 truncate">{n.title}</p>
                  <p className="text-xs text-slate-600 mt-0.5 leading-relaxed">{n.body}</p>
                  <p className="text-xs text-slate-400 mt-1">{n.time}</p>
                </div>
              </div>
            </div>
          ))}
        </div>
        <div className="px-5 py-4 border-t border-slate-100">
          <button className="w-full text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
            Mark all as read
          </button>
        </div>
      </div>
    </div>
  );
}
