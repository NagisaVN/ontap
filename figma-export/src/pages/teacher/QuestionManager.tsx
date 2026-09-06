import { useState } from "react";
import { Link } from "react-router-dom";
import { Pencil, Trash2, ChevronLeft, ChevronRight, ChevronDown, Plus } from "lucide-react";
import SearchInput from "../../components/ui/SearchInput";
import Badge from "../../components/ui/Badge";
import ConfirmModal from "../../components/ui/ConfirmModal";

const allQuestions = [
  { id: 1001, preview: "A particle moves along the x-axis with velocity v(t) = 3t² − 12t + 9 m/s...", subject: "Physics", topic: "Mechanics", difficulty: "Medium", created: "2026-09-01" },
  { id: 1002, preview: "Which of the following correctly describes the magnetic force on a moving charge?", subject: "Physics", topic: "Electromagnetism", difficulty: "Hard", created: "2026-08-30" },
  { id: 1003, preview: "What is the product of the reaction between ethanol and acetic acid in the presence of H₂SO₄?", subject: "Chemistry", topic: "Organic Chemistry", difficulty: "Medium", created: "2026-08-28" },
  { id: 1004, preview: "Given that f(x) = x³ − 6x² + 9x + 2, find all local maxima and minima.", subject: "Mathematics", topic: "Calculus", difficulty: "Hard", created: "2026-08-27" },
  { id: 1005, preview: "Which organelle is responsible for ATP synthesis in eukaryotic cells?", subject: "Biology", topic: "Cell Biology", difficulty: "Easy", created: "2026-08-26" },
  { id: 1006, preview: "State and explain Newton's third law of motion with a real-world example.", subject: "Physics", topic: "Mechanics", difficulty: "Easy", created: "2026-08-25" },
  { id: 1007, preview: "Solve the quadratic inequality: 2x² − 5x − 3 > 0.", subject: "Mathematics", topic: "Algebra", difficulty: "Medium", created: "2026-08-24" },
  { id: 1008, preview: "Describe the process of DNA replication and the role of DNA polymerase.", subject: "Biology", topic: "Genetics", difficulty: "Hard", created: "2026-08-23" },
];

const difficultyVariant = (d: string): "success" | "warning" | "error" => {
  if (d === "Easy") return "success";
  if (d === "Medium") return "warning";
  return "error";
};

const PAGE_SIZE = 5;

export default function QuestionManager() {
  const [search, setSearch] = useState("");
  const [subject, setSubject] = useState("");
  const [difficulty, setDifficulty] = useState("");
  const [page, setPage] = useState(1);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const filtered = allQuestions.filter((q) => {
    const matchSearch = search === "" || q.preview.toLowerCase().includes(search.toLowerCase());
    const matchSubject = subject === "" || q.subject === subject;
    const matchDiff = difficulty === "" || q.difficulty === difficulty;
    return matchSearch && matchSubject && matchDiff;
  });
  const totalPages = Math.ceil(filtered.length / PAGE_SIZE);
  const paginated = filtered.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

  return (
    <div className="p-6 max-w-5xl mx-auto space-y-5">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-bold text-slate-900">Question Manager</h1>
          <p className="text-sm text-slate-500 mt-0.5">{allQuestions.length} questions in the bank</p>
        </div>
        <Link
          to="/teacher/questions/create"
          className="flex items-center gap-2 h-9 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white text-sm font-semibold transition-colors"
        >
          <Plus size={14} /> Add Question
        </Link>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap gap-3">
        <SearchInput
          value={search}
          onChange={(v) => { setSearch(v); setPage(1); }}
          placeholder="Search questions…"
          className="w-64"
        />
        <div className="relative">
          <select
            value={subject}
            onChange={(e) => { setSubject(e.target.value); setPage(1); }}
            className="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"
          >
            <option value="">All subjects</option>
            {["Mathematics", "Physics", "Chemistry", "Biology"].map((s) => <option key={s}>{s}</option>)}
          </select>
          <ChevronDown size={12} className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
        </div>
        <div className="relative">
          <select
            value={difficulty}
            onChange={(e) => { setDifficulty(e.target.value); setPage(1); }}
            className="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"
          >
            <option value="">All difficulties</option>
            {["Easy", "Medium", "Hard"].map((d) => <option key={d}>{d}</option>)}
          </select>
          <ChevronDown size={12} className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
        </div>
      </div>

      {/* Table */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-slate-100 bg-slate-50">
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">ID</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Question</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Difficulty</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Created</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {paginated.length === 0 ? (
                <tr><td colSpan={6} className="px-4 py-8 text-center text-sm text-slate-500">No questions match your filters.</td></tr>
              ) : (
                paginated.map((q) => (
                  <tr key={q.id} className="hover:bg-slate-50 transition-colors">
                    <td className="px-4 py-3.5 text-slate-500 font-mono text-xs">#{q.id}</td>
                    <td className="px-4 py-3.5">
                      <p className="text-slate-900 line-clamp-2 max-w-xs text-xs leading-relaxed">{q.preview}</p>
                      <p className="text-[11px] text-slate-400 mt-0.5">{q.topic}</p>
                    </td>
                    <td className="px-4 py-3.5 text-slate-700 font-medium whitespace-nowrap">{q.subject}</td>
                    <td className="px-4 py-3.5">
                      <Badge variant={difficultyVariant(q.difficulty)}>{q.difficulty}</Badge>
                    </td>
                    <td className="px-4 py-3.5 text-slate-500 whitespace-nowrap">{q.created}</td>
                    <td className="px-4 py-3.5">
                      <div className="flex items-center gap-1">
                        <button className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                          <Pencil size={13} />
                        </button>
                        <button
                          onClick={() => setDeleteId(q.id)}
                          className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                        >
                          <Trash2 size={13} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        <div className="flex items-center justify-between px-4 py-3 border-t border-slate-100 bg-slate-50">
          <p className="text-xs text-slate-500">
            {filtered.length === 0 ? "0 results" : `${(page - 1) * PAGE_SIZE + 1}–${Math.min(page * PAGE_SIZE, filtered.length)} of ${filtered.length}`}
          </p>
          <div className="flex items-center gap-1">
            <button onClick={() => setPage(Math.max(1, page - 1))} disabled={page === 1} className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 disabled:opacity-40">
              <ChevronLeft size={14} />
            </button>
            {totalPages > 0 && Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
              <button key={p} onClick={() => setPage(p)} className={`w-7 h-7 rounded-lg text-xs font-semibold ${p === page ? "bg-indigo-600 text-white" : "text-slate-600 hover:bg-slate-100"}`}>{p}</button>
            ))}
            <button onClick={() => setPage(Math.min(totalPages, page + 1))} disabled={page === totalPages || totalPages === 0} className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 disabled:opacity-40">
              <ChevronRight size={14} />
            </button>
          </div>
        </div>
      </div>

      <ConfirmModal
        open={deleteId !== null}
        onClose={() => setDeleteId(null)}
        onConfirm={() => setDeleteId(null)}
        title="Delete question?"
        message={`Question #${deleteId} will be permanently removed. This cannot be undone.`}
        confirmLabel="Delete"
        danger
      />
    </div>
  );
}
