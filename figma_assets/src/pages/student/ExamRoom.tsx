import { useState, useEffect, useCallback } from "react";
import { useNavigate } from "react-router-dom";
import { Flag, ChevronLeft, ChevronRight, Clock, AlertCircle } from "lucide-react";

const TOTAL_QUESTIONS = 40;
const TOTAL_SECONDS = 45 * 60;

const mockQuestions = Array.from({ length: TOTAL_QUESTIONS }, (_, i) => ({
  id: i + 1,
  text: i === 0
    ? "A particle moves along the x-axis with velocity v(t) = 3t² − 12t + 9 m/s. At what time(s) does the particle momentarily stop?"
    : i === 1
    ? "Which of the following is a correct statement about the magnetic force on a moving charge?"
    : `Question ${i + 1}: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Which of the following best describes the given phenomenon?`,
  options: [
    { key: "A", text: i === 0 ? "t = 1 s and t = 3 s" : i === 1 ? "The force is parallel to the velocity." : "Option A for question " + (i + 1) },
    { key: "B", text: i === 0 ? "t = 2 s only" : i === 1 ? "The force is perpendicular to both velocity and field." : "Option B for question " + (i + 1) },
    { key: "C", text: i === 0 ? "t = 0 s and t = 4 s" : i === 1 ? "The force depends only on the magnitude of charge." : "Option C for question " + (i + 1) },
    { key: "D", text: i === 0 ? "t = 3 s only" : i === 1 ? "The force is always directed along the field." : "Option D for question " + (i + 1) },
  ],
}));

export default function ExamRoom() {
  const navigate = useNavigate();
  const [current, setCurrent] = useState(0);
  const [answers, setAnswers] = useState<Record<number, string>>({});
  const [flagged, setFlagged] = useState<Set<number>>(new Set());
  const [timeLeft, setTimeLeft] = useState(TOTAL_SECONDS);
  const [submitOpen, setSubmitOpen] = useState(false);

  useEffect(() => {
    if (timeLeft <= 0) { navigate("/student/exam/result"); return; }
    const id = setInterval(() => setTimeLeft((t) => t - 1), 1000);
    return () => clearInterval(id);
  }, [timeLeft, navigate]);

  const fmt = (s: number) => {
    const h = Math.floor(s / 3600);
    const m = Math.floor((s % 3600) / 60);
    const sec = s % 60;
    return h > 0
      ? `${h}:${String(m).padStart(2, "0")}:${String(sec).padStart(2, "0")}`
      : `${String(m).padStart(2, "0")}:${String(sec).padStart(2, "0")}`;
  };

  const q = mockQuestions[current];
  const answeredCount = Object.keys(answers).length;
  const flaggedCount = flagged.size;
  const timerWarning = timeLeft < 300;

  const toggleFlag = useCallback(() => {
    setFlagged((prev) => {
      const next = new Set(prev);
      if (next.has(current)) next.delete(current); else next.add(current);
      return next;
    });
  }, [current]);

  const getCellClass = (i: number) => {
    if (answers[i] !== undefined && flagged.has(i)) return "bg-amber-400 text-white border-amber-500";
    if (answers[i] !== undefined) return "bg-indigo-600 text-white border-indigo-700";
    if (flagged.has(i)) return "bg-amber-50 text-amber-700 border-amber-300";
    if (i === current) return "bg-slate-900 text-white border-slate-900";
    return "bg-white text-slate-600 border-slate-200 hover:border-indigo-300";
  };

  return (
    <div className="h-full flex flex-col bg-slate-50">
      {/* Sticky header */}
      <header className="sticky top-0 z-20 bg-white border-b border-slate-200 shadow-sm px-6 h-14 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <span className="font-bold text-slate-900 text-sm">Physics – Electromagnetism</span>
          <span className="text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-2 py-0.5 font-medium">40 questions</span>
        </div>
        <div className="flex items-center gap-4">
          <div className={`flex items-center gap-1.5 font-mono font-bold text-base px-3 py-1 rounded-lg ${timerWarning ? "bg-rose-50 text-rose-600" : "bg-slate-100 text-slate-700"}`}>
            <Clock size={14} />
            {fmt(timeLeft)}
          </div>
          <button
            onClick={() => setSubmitOpen(true)}
            className="h-8 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors"
          >
            Submit
          </button>
        </div>
      </header>

      <div className="flex flex-1 min-h-0">
        {/* Question panel */}
        <main className="flex-1 overflow-y-auto p-6">
          <div className="max-w-2xl mx-auto">
            <div className="flex items-center justify-between mb-4">
              <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Question {current + 1} of {TOTAL_QUESTIONS}
              </span>
              <button
                onClick={toggleFlag}
                className={`flex items-center gap-1.5 text-xs font-semibold px-3 h-7 rounded-lg border transition-colors ${
                  flagged.has(current)
                    ? "bg-amber-50 text-amber-700 border-amber-300"
                    : "text-slate-500 border-slate-200 hover:border-amber-300 hover:text-amber-700"
                }`}
              >
                <Flag size={11} /> {flagged.has(current) ? "Flagged" : "Flag for review"}
              </button>
            </div>

            <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-4">
              <p className="text-slate-900 text-sm leading-relaxed font-medium">{q.text}</p>
            </div>

            <div className="space-y-3">
              {q.options.map((opt) => {
                const selected = answers[current] === opt.key;
                return (
                  <button
                    key={opt.key}
                    onClick={() => setAnswers({ ...answers, [current]: opt.key })}
                    className={`w-full flex items-start gap-4 p-4 rounded-xl border-2 text-left transition-all ${
                      selected
                        ? "border-indigo-500 bg-indigo-50"
                        : "border-slate-200 bg-white hover:border-indigo-300"
                    }`}
                  >
                    <span className={`w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 border-2 mt-0.5 ${
                      selected ? "border-indigo-500 bg-indigo-500 text-white" : "border-slate-300 text-slate-500"
                    }`}>
                      {opt.key}
                    </span>
                    <span className={`text-sm leading-relaxed ${selected ? "text-indigo-900 font-medium" : "text-slate-700"}`}>
                      {opt.text}
                    </span>
                  </button>
                );
              })}
            </div>

            {/* Navigation */}
            <div className="flex items-center justify-between mt-6">
              <button
                onClick={() => setCurrent(Math.max(0, current - 1))}
                disabled={current === 0}
                className="flex items-center gap-2 h-9 px-4 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
              >
                <ChevronLeft size={14} /> Previous
              </button>
              <span className="text-xs text-slate-400">{answeredCount}/{TOTAL_QUESTIONS} answered · {flaggedCount} flagged</span>
              <button
                onClick={() => setCurrent(Math.min(TOTAL_QUESTIONS - 1, current + 1))}
                disabled={current === TOTAL_QUESTIONS - 1}
                className="flex items-center gap-2 h-9 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-semibold text-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
              >
                Next <ChevronRight size={14} />
              </button>
            </div>
          </div>
        </main>

        {/* Right sidebar — question grid */}
        <aside className="hidden lg:flex flex-col w-56 border-l border-slate-200 bg-white p-4 overflow-y-auto">
          <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Questions</p>
          <div className="grid grid-cols-5 gap-1.5">
            {mockQuestions.map((_, i) => (
              <button
                key={i}
                onClick={() => setCurrent(i)}
                className={`h-8 w-full rounded-lg border text-xs font-semibold transition-all ${getCellClass(i)}`}
              >
                {i + 1}
              </button>
            ))}
          </div>
          <div className="mt-4 space-y-2 text-xs text-slate-600">
            {[
              { color: "bg-indigo-600", label: "Answered" },
              { color: "bg-amber-400", label: "Flagged" },
              { color: "bg-white border border-slate-200", label: "Unanswered" },
            ].map((item) => (
              <div key={item.label} className="flex items-center gap-2">
                <div className={`w-3 h-3 rounded-sm ${item.color}`} />
                {item.label}
              </div>
            ))}
          </div>
        </aside>
      </div>

      {/* Submit confirm modal */}
      {submitOpen && (
        <div className="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl shadow-md w-full max-w-sm p-6">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <AlertCircle size={18} className="text-amber-600" />
              </div>
              <div>
                <p className="font-semibold text-slate-900">Submit exam?</p>
                <p className="text-xs text-slate-500 mt-0.5">You've answered {answeredCount} of {TOTAL_QUESTIONS} questions. {TOTAL_QUESTIONS - answeredCount} unanswered.</p>
              </div>
            </div>
            <div className="flex gap-3">
              <button onClick={() => setSubmitOpen(false)} className="flex-1 h-9 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Keep going
              </button>
              <button onClick={() => navigate("/student/exam/result")} className="flex-1 h-9 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-semibold text-white transition-colors">
                Submit
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
