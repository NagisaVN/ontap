import { FileQuestion, Clock, ScanLine, CheckCircle2, Upload, BookOpen, Pencil } from "lucide-react";
import KpiCard from "../../components/ui/KpiCard";

const timeline = [
  { actor: "Tran Minh Quan", action: "uploaded 12 new questions", subject: "Chemistry – Organic", time: "5 minutes ago", avatar: "MQ", color: "bg-emerald-100 text-emerald-700" },
  { actor: "Le Thi Hoa", action: "submitted an OCR batch", subject: "Physics – Mechanics (18 images)", time: "42 minutes ago", avatar: "TH", color: "bg-blue-100 text-blue-700" },
  { actor: "Nguyen Van Binh", action: "published exam paper", subject: "Math Final – September 2026", time: "2 hours ago", avatar: "VB", color: "bg-indigo-100 text-indigo-700" },
  { actor: "Pham Thi Lan", action: "edited 3 questions", subject: "Biology – Genetics", time: "3 hours ago", avatar: "TL", color: "bg-amber-100 text-amber-700" },
  { actor: "Admin System", action: "flagged 2 questions for review", subject: "Duplicate detection", time: "Yesterday", avatar: "AS", color: "bg-slate-100 text-slate-700" },
];

export default function TeacherDashboard() {
  return (
    <div className="p-6 max-w-4xl mx-auto space-y-6">
      <div>
        <h1 className="text-xl font-bold text-slate-900">Teacher Dashboard</h1>
        <p className="text-sm text-slate-500 mt-0.5">Manage questions, exams, and student reports.</p>
      </div>

      {/* Metrics */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <KpiCard label="Uploaded Questions" value="1,248" icon={<FileQuestion size={18} />} delta="24 this week" deltaPositive color="indigo" />
        <KpiCard label="Pending Reviews" value="17" icon={<Clock size={18} />} delta="↑ 5 since yesterday" deltaPositive={false} color="amber" />
        <KpiCard label="OCR Queue" value="6" icon={<ScanLine size={18} />} delta="Processing 2 now" deltaPositive color="blue" />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {/* Quick actions */}
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
          <h2 className="font-semibold text-slate-900 mb-4">Quick Actions</h2>
          <div className="space-y-2">
            {[
              { icon: <BookOpen size={14} />, label: "Add new question", href: "/teacher/questions/create", color: "text-indigo-600 bg-indigo-50 hover:bg-indigo-100" },
              { icon: <Upload size={14} />, label: "OCR upload", href: "/teacher/ocr-upload", color: "text-emerald-600 bg-emerald-50 hover:bg-emerald-100" },
              { icon: <Pencil size={14} />, label: "Build exam paper", href: "/teacher/exam-builder", color: "text-blue-600 bg-blue-50 hover:bg-blue-100" },
              { icon: <CheckCircle2 size={14} />, label: "Review pending", href: "/teacher/questions", color: "text-amber-600 bg-amber-50 hover:bg-amber-100" },
            ].map((a) => (
              <a
                key={a.label}
                href={a.href}
                className={`flex items-center gap-3 px-4 h-10 rounded-xl text-sm font-medium transition-colors ${a.color}`}
              >
                {a.icon} {a.label}
              </a>
            ))}
          </div>
        </div>

        {/* Activity timeline */}
        <div className="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
          <div className="px-5 pt-5 pb-3 border-b border-slate-100">
            <h2 className="font-semibold text-slate-900">Recent Activity</h2>
          </div>
          <div className="px-5 py-2 divide-y divide-slate-100">
            {timeline.map((item, i) => (
              <div key={i} className="flex items-start gap-3 py-3.5">
                <div className={`w-8 h-8 rounded-full ${item.color} flex items-center justify-center text-xs font-bold shrink-0 mt-0.5`}>
                  {item.avatar}
                </div>
                <div className="min-w-0">
                  <p className="text-sm text-slate-900">
                    <span className="font-semibold">{item.actor}</span>{" "}
                    <span className="text-slate-600">{item.action}</span>
                  </p>
                  <p className="text-xs text-slate-500 mt-0.5">{item.subject}</p>
                  <p className="text-[11px] text-slate-400 mt-1">{item.time}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
