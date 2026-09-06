import { useState } from "react";
import { ChevronLeft, ChevronRight, ChevronDown } from "lucide-react";
import Badge from "../../components/ui/Badge";

type ActionType = "CREATE" | "UPDATE" | "DELETE" | "LOGIN";

const actionMeta: Record<ActionType, { variant: "success" | "info" | "error" | "neutral"; label: string }> = {
  CREATE: { variant: "success", label: "CREATE" },
  UPDATE: { variant: "info", label: "UPDATE" },
  DELETE: { variant: "error", label: "DELETE" },
  LOGIN: { variant: "neutral", label: "LOGIN" },
};

const logs: { ts: string; actor: string; avatar: string; color: string; action: ActionType; description: string; target: string }[] = [
  { ts: "2026-09-03 14:32:11", actor: "Admin Bui", avatar: "BL", color: "bg-rose-100 text-rose-700", action: "DELETE", description: "Deleted question bank entry", target: "Question #1045" },
  { ts: "2026-09-03 13:18:45", actor: "Teacher Binh", avatar: "VB", color: "bg-indigo-100 text-indigo-700", action: "CREATE", description: "Published new exam paper", target: "Exam #EX-20260903-A" },
  { ts: "2026-09-03 12:55:02", actor: "Admin Bui", avatar: "BL", color: "bg-rose-100 text-rose-700", action: "UPDATE", description: "Changed user role", target: "User: Dao Quang Huy → teacher" },
  { ts: "2026-09-03 11:42:30", actor: "Teacher Lan", avatar: "TL", color: "bg-emerald-100 text-emerald-700", action: "CREATE", description: "Uploaded OCR batch", target: "OCR Batch #OCR-28 (18 images)" },
  { ts: "2026-09-03 10:31:00", actor: "Nguyen Giang", avatar: "NG", color: "bg-blue-100 text-blue-700", action: "LOGIN", description: "User logged in", target: "IP: 113.161.x.x" },
  { ts: "2026-09-03 09:15:22", actor: "Admin Bui", avatar: "BL", color: "bg-rose-100 text-rose-700", action: "UPDATE", description: "Updated taxonomy node", target: "Taxonomy: Physics → Quantum Mechanics added" },
  { ts: "2026-09-02 17:48:55", actor: "Teacher Binh", avatar: "VB", color: "bg-indigo-100 text-indigo-700", action: "UPDATE", description: "Edited question", target: "Question #1032" },
  { ts: "2026-09-02 16:22:10", actor: "Admin Bui", avatar: "BL", color: "bg-rose-100 text-rose-700", action: "DELETE", description: "Banned user account", target: "User: Hoang Thi Mai" },
  { ts: "2026-09-02 15:10:48", actor: "Teacher Lan", avatar: "TL", color: "bg-emerald-100 text-emerald-700", action: "CREATE", description: "Added 8 new questions", target: "Subject: Organic Chemistry" },
  { ts: "2026-09-02 14:05:33", actor: "System", avatar: "SY", color: "bg-slate-100 text-slate-600", action: "CREATE", description: "Automated backup completed", target: "Backup: db-2026-09-02.sql.gz" },
];

const PAGE_SIZE = 6;

export default function AuditLogs() {
  const [page, setPage] = useState(1);
  const [actionFilter, setActionFilter] = useState<string>("");
  const [dateFrom, setDateFrom] = useState("");

  const filtered = logs.filter((l) => {
    const ma = actionFilter === "" || l.action === actionFilter;
    const md = dateFrom === "" || l.ts.startsWith(dateFrom);
    return ma && md;
  });

  const totalPages = Math.ceil(filtered.length / PAGE_SIZE);
  const paginated = filtered.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

  return (
    <div className="p-6 max-w-5xl mx-auto space-y-5">
      <div>
        <h1 className="text-xl font-bold text-slate-900">Audit Logs</h1>
        <p className="text-sm text-slate-500 mt-0.5">Paginated record of all admin and teacher operations.</p>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap gap-3">
        <div>
          <label className="block text-xs font-medium text-slate-600 mb-1">Date (from)</label>
          <input
            type="date"
            value={dateFrom}
            onChange={(e) => { setDateFrom(e.target.value); setPage(1); }}
            className="h-9 px-3 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"
          />
        </div>
        <div>
          <label className="block text-xs font-medium text-slate-600 mb-1">Action type</label>
          <div className="relative">
            <select
              value={actionFilter}
              onChange={(e) => { setActionFilter(e.target.value); setPage(1); }}
              className="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"
            >
              <option value="">All actions</option>
              {(["CREATE", "UPDATE", "DELETE", "LOGIN"] as ActionType[]).map((a) => (
                <option key={a}>{a}</option>
              ))}
            </select>
            <ChevronDown size={12} className="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
          </div>
        </div>
      </div>

      {/* Log stream */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="divide-y divide-slate-100">
          {paginated.length === 0 ? (
            <div className="py-10 text-center text-sm text-slate-500">No log entries match your filters.</div>
          ) : (
            paginated.map((log, i) => (
              <div key={i} className="flex items-start gap-4 px-5 py-4 hover:bg-slate-50 transition-colors">
                {/* Avatar */}
                <div className={`w-8 h-8 rounded-full ${log.color} flex items-center justify-center text-xs font-bold shrink-0 mt-0.5`}>
                  {log.avatar}
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2 flex-wrap mb-1">
                    <span className="text-sm font-semibold text-slate-900">{log.actor}</span>
                    <Badge variant={actionMeta[log.action].variant}>{actionMeta[log.action].label}</Badge>
                    <span className="text-sm text-slate-600">{log.description}</span>
                  </div>
                  <p className="text-xs text-slate-500 truncate">{log.target}</p>
                </div>
                <span className="text-[11px] text-slate-400 whitespace-nowrap shrink-0 mt-1 font-mono">{log.ts}</span>
              </div>
            ))
          )}
        </div>

        {/* Pagination */}
        <div className="flex items-center justify-between px-5 py-3 border-t border-slate-100 bg-slate-50">
          <p className="text-xs text-slate-500">
            {filtered.length === 0 ? "0 entries" : `${(page - 1) * PAGE_SIZE + 1}–${Math.min(page * PAGE_SIZE, filtered.length)} of ${filtered.length}`}
          </p>
          <div className="flex items-center gap-1">
            <button onClick={() => setPage(Math.max(1, page - 1))} disabled={page === 1} className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 disabled:opacity-40">
              <ChevronLeft size={14} />
            </button>
            {Array.from({ length: Math.max(totalPages, 1) }, (_, i) => i + 1).map((p) => (
              <button key={p} onClick={() => setPage(p)} className={`w-7 h-7 rounded-lg text-xs font-semibold ${p === page ? "bg-indigo-600 text-white" : "text-slate-600 hover:bg-slate-100"}`}>{p}</button>
            ))}
            <button onClick={() => setPage(Math.min(totalPages, page + 1))} disabled={page === totalPages || totalPages === 0} className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 disabled:opacity-40">
              <ChevronRight size={14} />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
