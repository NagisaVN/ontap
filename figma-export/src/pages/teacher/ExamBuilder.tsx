import { useState } from "react";
import { Minus, Plus, CheckSquare, Square, RefreshCw, ChevronDown } from "lucide-react";
import Badge from "../../components/ui/Badge";
import SearchInput from "../../components/ui/SearchInput";

const questionBank = [
  { id: 1001, preview: "A particle moves with v(t) = 3t² − 12t + 9 m/s. When does it stop?", subject: "Physics", difficulty: "Medium" },
  { id: 1002, preview: "Describe the magnetic force on a moving charge in a uniform field.", subject: "Physics", difficulty: "Hard" },
  { id: 1003, preview: "What is the product of ethanol + acetic acid with H₂SO₄ catalyst?", subject: "Chemistry", difficulty: "Medium" },
  { id: 1004, preview: "Find local maxima/minima of f(x) = x³ − 6x² + 9x + 2.", subject: "Mathematics", difficulty: "Hard" },
  { id: 1005, preview: "Which organelle is responsible for ATP synthesis?", subject: "Biology", difficulty: "Easy" },
  { id: 1006, preview: "State Newton's third law with a real-world example.", subject: "Physics", difficulty: "Easy" },
  { id: 1007, preview: "Solve the inequality: 2x² − 5x − 3 > 0.", subject: "Mathematics", difficulty: "Medium" },
  { id: 1008, preview: "Describe DNA replication and the role of DNA polymerase.", subject: "Biology", difficulty: "Hard" },
];

const diffVariant = (d: string): "success" | "warning" | "error" =>
  d === "Easy" ? "success" : d === "Medium" ? "warning" : "error";

function generateCode() {
  return Math.random().toString(36).substring(2, 8).toUpperCase();
}

export default function ExamBuilder() {
  const [search, setSearch] = useState("");
  const [subject, setSubject] = useState("");
  const [difficulty, setDifficulty] = useState("");
  const [selected, setSelected] = useState<number[]>([]);
  const [title, setTitle] = useState("Physics – September Midterm 2026");
  const [timeLimit, setTimeLimit] = useState(60);
  const [maxAttempts, setMaxAttempts] = useState(1);
  const [examCode] = useState(generateCode());

  const filtered = questionBank.filter((q) => {
    const ms = search === "" || q.preview.toLowerCase().includes(search.toLowerCase());
    const msub = subject === "" || q.subject === subject;
    const mdiff = difficulty === "" || q.difficulty === difficulty;
    return ms && msub && mdiff;
  });

  const toggle = (id: number) =>
    setSelected((prev) => prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]);

  const selectedQuestions = questionBank.filter((q) => selected.includes(q.id));

  return (
    <div className="p-6 max-w-5xl mx-auto">
      <h1 className="text-xl font-bold text-slate-900 mb-1">Exam Builder</h1>
      <p className="text-sm text-slate-500 mb-6">Select questions from the bank and configure the exam paper.</p>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {/* Question bank */}
        <div className="space-y-3">
          <h2 className="text-sm font-semibold text-slate-700">Question Bank</h2>
          <div className="flex flex-wrap gap-2">
            <SearchInput value={search} onChange={setSearch} className="flex-1 min-w-40" />
            <div className="relative">
              <select value={subject} onChange={(e) => setSubject(e.target.value)} className="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                <option value="">All subjects</option>
                {["Mathematics", "Physics", "Chemistry", "Biology"].map((s) => <option key={s}>{s}</option>)}
              </select>
              <ChevronDown size={12} className="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
            </div>
            <div className="relative">
              <select value={difficulty} onChange={(e) => setDifficulty(e.target.value)} className="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                <option value="">All levels</option>
                {["Easy", "Medium", "Hard"].map((d) => <option key={d}>{d}</option>)}
              </select>
              <ChevronDown size={12} className="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
            </div>
          </div>
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100 max-h-[480px] overflow-y-auto">
            {filtered.map((q) => {
              const isSelected = selected.includes(q.id);
              return (
                <div
                  key={q.id}
                  onClick={() => toggle(q.id)}
                  className={`flex items-start gap-3 px-4 py-3.5 cursor-pointer transition-colors ${isSelected ? "bg-indigo-50" : "hover:bg-slate-50"}`}
                >
                  {isSelected ? (
                    <CheckSquare size={16} className="text-indigo-600 shrink-0 mt-0.5" />
                  ) : (
                    <Square size={16} className="text-slate-300 shrink-0 mt-0.5" />
                  )}
                  <div className="min-w-0">
                    <p className="text-xs text-slate-800 leading-relaxed line-clamp-2">{q.preview}</p>
                    <div className="flex items-center gap-2 mt-1.5">
                      <span className="text-[10px] text-slate-500 font-medium">{q.subject}</span>
                      <Badge variant={diffVariant(q.difficulty)} className="text-[10px] py-0">{q.difficulty}</Badge>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Config panel */}
        <div className="space-y-4">
          {/* Selected */}
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div className="flex items-center justify-between mb-3">
              <h2 className="text-sm font-semibold text-slate-700">Selected Questions</h2>
              <span className="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">{selected.length} selected</span>
            </div>
            {selectedQuestions.length === 0 ? (
              <p className="text-xs text-slate-400 italic py-4 text-center">No questions selected yet.</p>
            ) : (
              <ul className="space-y-1.5 max-h-40 overflow-y-auto">
                {selectedQuestions.map((q, i) => (
                  <li key={q.id} className="flex items-center gap-2 text-xs text-slate-700">
                    <span className="text-slate-400 w-4 shrink-0">{i + 1}.</span>
                    <span className="truncate">{q.preview}</span>
                    <button onClick={() => toggle(q.id)} className="ml-auto text-slate-300 hover:text-rose-500 shrink-0">
                      <Minus size={12} />
                    </button>
                  </li>
                ))}
              </ul>
            )}
          </div>

          {/* Exam config */}
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h2 className="text-sm font-semibold text-slate-700">Exam Configuration</h2>
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Exam title</label>
              <input value={title} onChange={(e) => setTitle(e.target.value)} className="w-full h-9 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400" />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-xs font-medium text-slate-600 mb-1.5">Time limit (min)</label>
                <div className="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                  <button onClick={() => setTimeLimit(Math.max(5, timeLimit - 5))} className="w-9 h-9 flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><Minus size={13} /></button>
                  <span className="flex-1 text-center text-sm font-semibold text-slate-900">{timeLimit}</span>
                  <button onClick={() => setTimeLimit(Math.min(180, timeLimit + 5))} className="w-9 h-9 flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><Plus size={13} /></button>
                </div>
              </div>
              <div>
                <label className="block text-xs font-medium text-slate-600 mb-1.5">Max attempts</label>
                <div className="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                  <button onClick={() => setMaxAttempts(Math.max(1, maxAttempts - 1))} className="w-9 h-9 flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><Minus size={13} /></button>
                  <span className="flex-1 text-center text-sm font-semibold text-slate-900">{maxAttempts}</span>
                  <button onClick={() => setMaxAttempts(Math.min(10, maxAttempts + 1))} className="w-9 h-9 flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><Plus size={13} /></button>
                </div>
              </div>
            </div>
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Exam code</label>
              <div className="flex items-center gap-2">
                <div className="flex-1 h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center font-mono text-sm font-bold text-slate-900 tracking-widest">
                  {examCode}
                </div>
                <button className="w-9 h-9 flex items-center justify-center border border-slate-200 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                  <RefreshCw size={13} />
                </button>
              </div>
            </div>
          </div>

          <button
            disabled={selected.length === 0}
            className="w-full h-11 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl text-white text-sm font-bold transition-colors"
          >
            Generate Exam Paper ({selected.length} questions)
          </button>
        </div>
      </div>
    </div>
  );
}
