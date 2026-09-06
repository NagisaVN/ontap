import { Link } from "react-router-dom";
import { BookOpen, Target, Zap, ChevronRight, ArrowRight } from "lucide-react";
import KpiCard from "../../components/ui/KpiCard";

const continueItems = [
  { subject: "Mathematics", topic: "Integral Calculus", progress: 72, questions: 28, total: 40 },
  { subject: "Physics", topic: "Electromagnetism – Induction", progress: 45, questions: 18, total: 40 },
  { subject: "Chemistry", topic: "Organic Chemistry – Alcohols", progress: 90, questions: 36, total: 40 },
];

// 7-week streak heatmap data (0-4 intensity)
const streakData = [
  [1, 2, 0, 3, 4, 2, 1],
  [0, 1, 3, 2, 1, 0, 2],
  [2, 3, 4, 3, 2, 3, 4],
  [1, 0, 2, 1, 3, 2, 0],
  [3, 4, 3, 2, 4, 3, 2],
  [0, 1, 0, 3, 1, 2, 1],
  [2, 1, 3, 4, 2, 1, 3],
];
const days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
const weeks = ["W1", "W2", "W3", "W4", "W5", "W6", "W7"];

const intensityClass = (v: number) => {
  if (v === 0) return "bg-slate-100";
  if (v === 1) return "bg-indigo-100";
  if (v === 2) return "bg-indigo-300";
  if (v === 3) return "bg-indigo-500";
  return "bg-indigo-700";
};

export default function StudentDashboard() {
  const now = new Date();
  const hour = now.getHours();
  const greeting = hour < 12 ? "Good morning" : hour < 18 ? "Good afternoon" : "Good evening";

  return (
    <div className="p-6 max-w-5xl mx-auto space-y-6">
      {/* Greeting banner */}
      <div className="bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-2xl p-6 flex items-center justify-between text-white shadow-sm">
        <div>
          <p className="text-indigo-200 text-sm font-medium">
            {now.toLocaleDateString("en-GB", { weekday: "long", year: "numeric", month: "long", day: "numeric" })}
          </p>
          <h1 className="text-2xl font-bold mt-0.5">{greeting}, Nguyen Giang! 👋</h1>
          <p className="text-indigo-100 text-sm mt-1">You have 3 exams scheduled this week. Let's keep the streak going.</p>
        </div>
        <Link
          to="/student/exam/setup"
          className="hidden sm:flex items-center gap-2 bg-white/20 hover:bg-white/30 transition-colors rounded-xl px-4 h-10 text-sm font-semibold text-white shrink-0"
        >
          Start Exam <ArrowRight size={14} />
        </Link>
      </div>

      {/* KPI cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <KpiCard label="Total Exams Taken" value="47" icon={<BookOpen size={18} />} delta="5 this week" deltaPositive color="indigo" />
        <KpiCard label="Average Score" value="78.4%" icon={<Target size={18} />} delta="2.3% vs last week" deltaPositive color="blue" />
        <KpiCard label="Accuracy Rate" value="81%" icon={<Zap size={18} />} delta="1.2% vs last week" deltaPositive color="emerald" />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {/* Continue learning */}
        <div className="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
          <div className="flex items-center justify-between px-5 pt-5 pb-3 border-b border-slate-100">
            <h2 className="font-semibold text-slate-900">Continue Learning</h2>
            <Link to="/student/history" className="text-xs font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
              View all <ChevronRight size={12} />
            </Link>
          </div>
          <div className="divide-y divide-slate-100">
            {continueItems.map((item) => (
              <div key={item.topic} className="px-5 py-4 flex items-center gap-4">
                <div className="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                  <BookOpen size={16} className="text-indigo-600" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-semibold text-slate-900 truncate">{item.topic}</p>
                  <p className="text-xs text-slate-500">{item.subject}</p>
                  <div className="mt-2 flex items-center gap-2">
                    <div className="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                      <div
                        className="h-full bg-indigo-500 rounded-full"
                        style={{ width: `${item.progress}%` }}
                      />
                    </div>
                    <span className="text-xs text-slate-500 shrink-0">{item.questions}/{item.total}</span>
                  </div>
                </div>
                <Link
                  to="/student/exam/room"
                  className="shrink-0 h-8 px-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-xs font-semibold text-indigo-700 transition-colors flex items-center"
                >
                  Continue
                </Link>
              </div>
            ))}
          </div>
        </div>

        {/* Streak heatmap */}
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
          <div className="flex items-center justify-between mb-4">
            <h2 className="font-semibold text-slate-900">Study Streak</h2>
            <span className="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">🔥 14 days</span>
          </div>

          {/* Day labels */}
          <div className="grid grid-cols-7 gap-1 mb-1">
            {days.map((d) => (
              <div key={d} className="text-center text-[10px] text-slate-400 font-medium">{d[0]}</div>
            ))}
          </div>

          {/* Grid */}
          <div className="space-y-1">
            {streakData.map((week, wi) => (
              <div key={wi} className="grid grid-cols-7 gap-1">
                {week.map((val, di) => (
                  <div
                    key={di}
                    title={`${weeks[wi]} ${days[di]}: ${val * 10} mins`}
                    className={`h-6 rounded-sm ${intensityClass(val)} transition-colors`}
                  />
                ))}
              </div>
            ))}
          </div>

          <div className="flex items-center justify-end gap-1.5 mt-3">
            <span className="text-[10px] text-slate-400">Less</span>
            {[0, 1, 2, 3, 4].map((v) => (
              <div key={v} className={`w-3 h-3 rounded-sm ${intensityClass(v)}`} />
            ))}
            <span className="text-[10px] text-slate-400">More</span>
          </div>
        </div>
      </div>
    </div>
  );
}
