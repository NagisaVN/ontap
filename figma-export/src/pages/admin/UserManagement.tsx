import { useState } from "react";
import { ChevronDown, Trash2, UserX, UserCheck } from "lucide-react";
import SearchInput from "../../components/ui/SearchInput";
import Badge from "../../components/ui/Badge";
import ConfirmModal from "../../components/ui/ConfirmModal";

const allUsers = [
  { id: 1, name: "Tran Minh Quan", email: "quan.tran@example.com", avatar: "MQ", role: "student", status: "active", joined: "2026-01-15" },
  { id: 2, name: "Le Thi Hoa", email: "hoa.le@example.com", avatar: "TH", role: "student", status: "active", joined: "2026-02-08" },
  { id: 3, name: "Nguyen Van Binh", email: "binh.nguyen@example.com", avatar: "VB", role: "teacher", status: "active", joined: "2025-11-20" },
  { id: 4, name: "Pham Thi Lan", email: "lan.pham@example.com", avatar: "TL", role: "teacher", status: "active", joined: "2025-10-05" },
  { id: 5, name: "Hoang Thi Mai", email: "mai.hoang@example.com", avatar: "TM", role: "student", status: "banned", joined: "2026-03-12" },
  { id: 6, name: "Dao Quang Huy", email: "huy.dao@example.com", avatar: "QH", role: "student", status: "active", joined: "2026-04-22" },
  { id: 7, name: "Bui Thi Lan", email: "lan.bui@example.com", avatar: "TL2", role: "admin", status: "active", joined: "2025-08-01" },
];

const roles = ["student", "teacher", "admin"];

export default function UserManagement() {
  const [search, setSearch] = useState("");
  const [roleFilter, setRoleFilter] = useState("");
  const [users, setUsers] = useState(allUsers);
  const [selected, setSelected] = useState<number[]>([]);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const filtered = users.filter((u) => {
    const ms = search === "" || u.name.toLowerCase().includes(search.toLowerCase()) || u.email.toLowerCase().includes(search.toLowerCase());
    const mr = roleFilter === "" || u.role === roleFilter;
    return ms && mr;
  });

  const toggleSelect = (id: number) =>
    setSelected((prev) => prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]);

  const toggleBan = (id: number) =>
    setUsers((prev) => prev.map((u) => u.id === id ? { ...u, status: u.status === "banned" ? "active" : "banned" } : u));

  const changeRole = (id: number, role: string) =>
    setUsers((prev) => prev.map((u) => u.id === id ? { ...u, role } : u));

  const allSelected = filtered.length > 0 && filtered.every((u) => selected.includes(u.id));
  const toggleAll = () => setSelected(allSelected ? [] : filtered.map((u) => u.id));

  return (
    <div className="p-6 max-w-5xl mx-auto space-y-5">
      <div>
        <h1 className="text-xl font-bold text-slate-900">User Management</h1>
        <p className="text-sm text-slate-500 mt-0.5">{users.length} registered users</p>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap gap-3">
        <SearchInput value={search} onChange={setSearch} placeholder="Search users…" className="w-60" />
        <div className="relative">
          <select value={roleFilter} onChange={(e) => setRoleFilter(e.target.value)} className="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
            <option value="">All roles</option>
            {roles.map((r) => <option key={r} className="capitalize">{r}</option>)}
          </select>
          <ChevronDown size={12} className="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
        </div>
      </div>

      {/* Bulk action bar */}
      {selected.length > 0 && (
        <div className="flex items-center gap-3 px-4 py-2.5 bg-indigo-50 border border-indigo-200 rounded-xl">
          <span className="text-sm font-semibold text-indigo-700">{selected.length} selected</span>
          <div className="flex gap-2 ml-auto">
            <button className="flex items-center gap-1.5 h-7 px-3 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
              <Trash2 size={11} /> Delete
            </button>
            <button className="flex items-center gap-1.5 h-7 px-3 rounded-lg text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-colors">
              <UserX size={11} /> Ban all
            </button>
            <button onClick={() => setSelected([])} className="h-7 px-3 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
              Clear
            </button>
          </div>
        </div>
      )}

      {/* Table */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-slate-100 bg-slate-50">
                <th className="px-4 py-3 w-10">
                  <input type="checkbox" checked={allSelected} onChange={toggleAll} className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                </th>
                {["User", "Email", "Role", "Status", "Joined", "Actions"].map((h) => (
                  <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filtered.map((user) => (
                <tr key={user.id} className={`transition-colors ${selected.includes(user.id) ? "bg-indigo-50/60" : "hover:bg-slate-50"}`}>
                  <td className="px-4 py-3.5">
                    <input type="checkbox" checked={selected.includes(user.id)} onChange={() => toggleSelect(user.id)} className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                  </td>
                  <td className="px-4 py-3.5">
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <span className="text-xs font-bold text-indigo-700">{user.avatar.slice(0, 2)}</span>
                      </div>
                      <span className="font-medium text-slate-900">{user.name}</span>
                    </div>
                  </td>
                  <td className="px-4 py-3.5 text-slate-600 text-xs">{user.email}</td>
                  <td className="px-4 py-3.5">
                    <div className="relative w-24">
                      <select
                        value={user.role}
                        onChange={(e) => changeRole(user.id, e.target.value)}
                        className="w-full h-7 pl-2 pr-6 appearance-none bg-white border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 capitalize"
                      >
                        {roles.map((r) => <option key={r} className="capitalize">{r}</option>)}
                      </select>
                      <ChevronDown size={10} className="absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    </div>
                  </td>
                  <td className="px-4 py-3.5">
                    <Badge variant={user.status === "active" ? "success" : "error"}>
                      {user.status}
                    </Badge>
                  </td>
                  <td className="px-4 py-3.5 text-slate-500 text-xs whitespace-nowrap">{user.joined}</td>
                  <td className="px-4 py-3.5">
                    <div className="flex items-center gap-1">
                      <button
                        onClick={() => toggleBan(user.id)}
                        title={user.status === "banned" ? "Unban" : "Ban"}
                        className={`w-7 h-7 rounded-lg flex items-center justify-center transition-colors ${
                          user.status === "banned"
                            ? "text-emerald-600 hover:bg-emerald-50"
                            : "text-amber-500 hover:bg-amber-50"
                        }`}
                      >
                        {user.status === "banned" ? <UserCheck size={13} /> : <UserX size={13} />}
                      </button>
                      <button
                        onClick={() => setDeleteId(user.id)}
                        className="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                      >
                        <Trash2 size={13} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <ConfirmModal
        open={deleteId !== null}
        onClose={() => setDeleteId(null)}
        onConfirm={() => setDeleteId(null)}
        title="Delete user?"
        message="This user and all their data will be permanently deleted. This action cannot be undone."
        confirmLabel="Delete user"
        danger
      />
    </div>
  );
}
