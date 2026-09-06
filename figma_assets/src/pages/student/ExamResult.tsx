import { Link } from "react-router-dom";
import { CheckCircle2, XCircle, MinusCircle, Clock, RotateCcw, Home } from "lucide-react";
import Badge from "../../components/ui/Badge";

const score = 32;
const total = 40;
const percentage = Math.round((score / total) * 100);
const circumference = 2 * Math.PI * 52;
const progress = circumference - (percentage / 100) * circumference;

const reviewData = [
  { q: "A particle moves along the x-axis with velocity v(t) = 3t² − 12t + 9 m/s. At what time(s) does the particle momentarily stop?", user: "A", correct: "A", explanation: "Setting v(t) = 0: 3t² − 12t + 9 = 0 → t = 1 s and t = 3 s." },
  { q: "Which of the following is a correct statement about the magnetic force on a moving charge?", user: "A", correct: "B", explanation: "The Lorentz force F = qv × B is always perpendicular to both the velocity v and the magnetic field B." },
  { q: "An electric field E points in the +x direction. A positive charge placed in this field will experience a force in which direction?", user: null, correct: "A", explanation: "Force on a positive charge F = qE, so it acts in the same direction as E, which is +x." },
  { q: "Which of the following materials is a diamagnet?", user: "C", correct: "D", explanation: "Bismuth is a classic diamagnetic material. It is repelled by magnetic fields." },
  { q: "What is the SI unit of electric flux?", user: "B", correct: "B", explanation: "Electric flux is measured in N·m²/C or equivalently V·m." },
];

function getStatus(user: string | null, correct: string) {
  if (user === null) return "skipped";
  if (user === correct) return "correct";
  return "wrong";
}

export default function ExamResult() {
  return (
    <div className="p-6 max-w-3xl mx-auto space-y-6">
      {/* Score card */}
      <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <h1 className="text-xl font-bold text-slate-900 mb-1">Exam Complete</h1>
        <p className="text-sm text-slate-500 mb-8">Physics – Electromagnetism &nbsp;·&nbsp; 45 minutes</p>

        <div className="flex flex-col items-center mb-8">
          {/* SVG circular progress */}
          <div className="relative w-36 h-36">
            <svg className="w-full h-full -rotate-90" viewBox="0 0 120 120">
              <circle cx="60" cy="60" r="52" fill="none" stroke="#e2e8f0" strokeWidth="10" />
              <circle
                cx="60" cy="60" r="52"
                fill="none"
                stroke={percentage >= 70 ? "#4f46e5" : percentage >= 50 ? "#f59e0b" : "#f43f5e"}
                strokeWidth="10"
                strokeDasharray={circumference}
                strokeDashoffset={progress}
                strokeLinecap="round"
              />
            </svg>
            <div className="absolute inset-0 flex flex-col items-center justify-center">
              <span className="text-3xl font-bold text-slate-900">{percentage}%</span>
              <span className="text-xs text-slate-500 font-medium">{score}/{total}</span>
            </div>
          </div>

          <Badge
            variant={percentage >= 70 ? "primary" : percentage >= 50 ? "warning" : "error"}
            className="mt-4 text-sm px-3 py-1"
          >
            {percentage >= 80 ? "Excellent" : percentage >= 70 ? "Good" : percentage >= 50 ? "Needs Work" : "Review Required"}
          </Badge>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
          {[
            { icon: <CheckCircle2 size={16} />, label: "Correct", value: score, color: "text-emerald-600 bg-emerald-50" },
            { icon: <XCircle size={16} />, label: "Wrong", value: total - score - 1, color: "text-rose-600 bg-rose-50" },
            { icon: <MinusCircle size={16} />, label: "Skipped", value: 1, color: "text-slate-500 bg-slate-100" },
            { icon: <Clock size={16} />, label: "Time used", value: "38m 12s", color: "text-blue-600 bg-blue-50" },
          ].map((s) => (
            <div key={s.label} className="flex flex-col items-center justify-center p-4 rounded-xl bg-slate-50 border border-slate-100">
              <div className={`w-8 h-8 rounded-lg ${s.color} flex items-center justify-center mb-2`}>
                {s.icon}
              </div>
              <span className="text-xl font-bold text-slate-900">{s.value}</span>
              <span className="text-xs text-slate-500 mt-0.5">{s.label}</span>
            </div>
          ))}
        </div>
      </div>

      {/* Action buttons */}
      <div className="flex gap-3">
        <Link to="/student/exam/setup" className="flex-1 flex items-center justify-center gap-2 h-10 bg-indigo-600 hover:bg-indigo-700 rounded-xl text-white text-sm font-semibold transition-colors">
          <RotateCcw size={14} /> Retake
        </Link>
        <Link to="/student/dashboard" className="flex-1 flex items-center justify-center gap-2 h-10 border border-slate-200 rounded-xl text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
          <Home size={14} /> Dashboard
        </Link>
      </div>

      {/* Answer review */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="px-5 py-4 border-b border-slate-100">
          <h2 className="font-semibold text-slate-900">Answer Review</h2>
          <p className="text-xs text-slate-500 mt-0.5">Showing first 5 questions for brevity.</p>
        </div>
        <div className="divide-y divide-slate-100">
          {reviewData.map((item, i) => {
            const status = getStatus(item.user, item.correct);
            return (
              <div key={i} className="px-5 py-4">
                <div className="flex items-start gap-3 mb-3">
                  <div className={`w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5 ${
                    status === "correct" ? "bg-emerald-50" : status === "wrong" ? "bg-rose-50" : "bg-slate-100"
                  }`}>
                    {status === "correct" && <CheckCircle2 size={13} className="text-emerald-600" />}
                    {status === "wrong" && <XCircle size={13} className="text-rose-600" />}
                    {status === "skipped" && <MinusCircle size={13} className="text-slate-500" />}
                  </div>
                  <p className="text-sm text-slate-800 font-medium leading-snug">{item.q}</p>
                </div>
                <div className="ml-9 flex flex-wrap gap-2 mb-2">
                  {item.user && (
                    <span className={`text-xs font-semibold px-2 py-1 rounded-lg border ${
                      status === "correct" ? "bg-emerald-50 text-emerald-700 border-emerald-200" : "bg-rose-50 text-rose-700 border-rose-200"
                    }`}>
                      Your answer: {item.user}
                    </span>
                  )}
                  {status !== "correct" && (
                    <span className="text-xs font-semibold px-2 py-1 rounded-lg border bg-emerald-50 text-emerald-700 border-emerald-200">
                      Correct: {item.correct}
                    </span>
                  )}
                  {status === "skipped" && (
                    <span className="text-xs font-semibold px-2 py-1 rounded-lg border bg-slate-100 text-slate-600 border-slate-200">
                      Not answered
                    </span>
                  )}
                </div>
                <p className="ml-9 text-xs text-slate-500 leading-relaxed">{item.explanation}</p>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}
