import { useState } from "react";
import { Link } from "react-router-dom";
import { ChevronLeft, ChevronRight, Eye } from "lucide-react";
import {
  RadarChart, PolarGrid, PolarAngleAxis, Radar, ResponsiveContainer, Tooltip
} from "recharts";
import Badge from "../../components/ui/Badge";

const radarData = [
  { subject: "Mathematics", score: 82 },
  { subject: "Physics", score: 74 },
  { subject: "Chemistry", score: 68 },
  { subject: "Biology", score: 91 },
  { subject: "Literature", score: 55 },
  { subject: "History", score: 78 },
];

const examLog = [
  { date: "2026-09-01", subject: "Physics – Electromagnetism", score: 80, duration: "38m", grade: "B+" },
  { date: "2026-08-28", subject: "Mathematics – Calculus", score: 95, duration: "42m", grade: "A" },
  { date: "2026-08-25", subject: "Chemistry – Organic", score: 62, duration: "30m", grade: "C+" },
  { date: "2026-08-22", subject: "Biology – Genetics", score: 88, duration: "45m", grade: "A-" },
  { date: "2026-08-20", subject: "Literature – Vietnamese", score: 71, duration: "35m", grade: "B" },
  { date: "2026-08-17", subject: "History – Vietnamese", score: 77, duration: "28m", grade: "B+" },
  { date: "2026-08-15", subject: "Physics – Mechanics", score: 85, duration: "40m", grade: "A-" },
  { date: "2026-08-12", subject: "Mathematics – Algebra", score: 92, duration: "33m", grade: "A" },
];

const gradeVariant = (g: string): "success" | "info" | "warning" | "error" => {
  if (g.startsWith("A")) return "success";
  if (g.startsWith("B")) return "info";
  if (g.startsWith("C")) return "warning";
  return "error";
};

const PAGE_SIZE = 5;

export default function History() {
  const [page, setPage] = useState(1);
  const totalPages = Math.ceil(examLog.length / PAGE_SIZE);
  const paginated = examLog.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

  return (
    <div className="p-6 max-w-4xl mx-auto space-y-6">
      <h1 className="text-xl font-bold text-slate-900">History & Analytics</h1>

      {/* Radar chart */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 className="font-semibold text-slate-900 mb-1">Subject Strengths</h2>
        <p className="text-xs text-slate-500 mb-4">Average score by subject across all attempts.</p>
        <div className="h-72">
          <ResponsiveContainer width="100%" height="100%">
            <RadarChart data={radarData} margin={{ top: 10, right: 30, bottom: 10, left: 30 }}>
              <PolarGrid stroke="#e2e8f0" />
              <PolarAngleAxis dataKey="subject" tick={{ fontSize: 11, fill: "#64748b" }} />
              <Tooltip
                formatter={(v: number) => [`${v}%`, "Score"]}
                contentStyle={{ fontSize: 12, borderRadius: 8, border: "1px solid #e2e8f0" }}
              />
              <Radar
                name="Score"
                dataKey="score"
                stroke="#4f46e5"
                fill="#4f46e5"
                fillOpacity={0.15}
                strokeWidth={2}
              />
            </RadarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Exam log table */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="px-5 py-4 border-b border-slate-100">
          <h2 className="font-semibold text-slate-900">Past Exams</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-slate-100 bg-slate-50">
                {["Date", "Subject", "Score", "Duration", "Grade", ""].map((h) => (
                  <th key={h} className="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {paginated.map((row, i) => (
                <tr key={i} className="hover:bg-slate-50 transition-colors">
                  <td className="px-5 py-3.5 text-slate-600 font-medium whitespace-nowrap">{row.date}</td>
                  <td className="px-5 py-3.5 text-slate-900">{row.subject}</td>
                  <td className="px-5 py-3.5">
                    <span className={`font-bold ${row.score >= 80 ? "text-emerald-600" : row.score >= 60 ? "text-amber-600" : "text-rose-600"}`}>
                      {row.score}%
                    </span>
                  </td>
                  <td className="px-5 py-3.5 text-slate-500">{row.duration}</td>
                  <td className="px-5 py-3.5">
                    <Badge variant={gradeVariant(row.grade)}>{row.grade}</Badge>
                  </td>
                  <td className="px-5 py-3.5">
                    <Link to="/student/exam/result" className="flex items-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-700">
                      <Eye size={12} /> View
                    </Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        <div className="flex items-center justify-between px-5 py-3 border-t border-slate-100 bg-slate-50">
          <p className="text-xs text-slate-500">
            Showing {(page - 1) * PAGE_SIZE + 1}–{Math.min(page * PAGE_SIZE, examLog.length)} of {examLog.length}
          </p>
          <div className="flex items-center gap-1">
            <button
              onClick={() => setPage(Math.max(1, page - 1))}
              disabled={page === 1}
              className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 disabled:opacity-40 transition-colors"
            >
              <ChevronLeft size={14} />
            </button>
            {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
              <button
                key={p}
                onClick={() => setPage(p)}
                className={`w-7 h-7 rounded-lg text-xs font-semibold transition-colors ${
                  p === page ? "bg-indigo-600 text-white" : "text-slate-600 hover:bg-slate-100"
                }`}
              >
                {p}
              </button>
            ))}
            <button
              onClick={() => setPage(Math.min(totalPages, page + 1))}
              disabled={page === totalPages}
              className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 disabled:opacity-40 transition-colors"
            >
              <ChevronRight size={14} />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
