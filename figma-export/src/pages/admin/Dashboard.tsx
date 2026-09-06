import { Users, Activity, Server, TrendingUp } from "lucide-react";
import KpiCard from "../../components/ui/KpiCard";
import {
  LineChart, Line, ResponsiveContainer, Tooltip, XAxis
} from "recharts";

const mrrData = [
  { month: "Mar", mrr: 12400 },
  { month: "Apr", mrr: 14200 },
  { month: "May", mrr: 13800 },
  { month: "Jun", mrr: 15600 },
  { month: "Jul", mrr: 17100 },
  { month: "Aug", mrr: 18400 },
  { month: "Sep", mrr: 21200 },
];

const serverMetrics = [
  { label: "CPU Usage", value: 42, color: "bg-indigo-500" },
  { label: "Memory", value: 67, color: "bg-blue-500" },
  { label: "Disk I/O", value: 28, color: "bg-emerald-500" },
  { label: "Network", value: 55, color: "bg-amber-500" },
];

export default function AdminDashboard() {
  return (
    <div className="p-6 max-w-5xl mx-auto space-y-6">
      <div>
        <h1 className="text-xl font-bold text-slate-900">Admin Dashboard</h1>
        <p className="text-sm text-slate-500 mt-0.5">System-wide analytics and platform health.</p>
      </div>

      {/* KPI cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <KpiCard label="Total Users" value="8,341" icon={<Users size={18} />} delta="214 this week" deltaPositive color="indigo" />
        <KpiCard label="Active Sessions" value="412" icon={<Activity size={18} />} delta="Live now" deltaPositive color="emerald" />
        <KpiCard label="Server Load" value="42%" icon={<Server size={18} />} delta="Healthy" deltaPositive color="blue" />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {/* MRR Sparkline Card */}
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
          <div className="flex items-center justify-between mb-1">
            <h2 className="font-semibold text-slate-900">Monthly Recurring Revenue</h2>
            <TrendingUp size={16} className="text-emerald-600" />
          </div>
          <p className="text-3xl font-bold text-slate-900 mb-0.5">21,200,000đ</p>
          <p className="text-xs text-emerald-600 font-medium mb-4">↑ 15.2% vs last month</p>
          <div className="h-28">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={mrrData} margin={{ top: 4, right: 4, bottom: 0, left: 4 }}>
                <XAxis dataKey="month" tick={{ fontSize: 10, fill: "#94a3b8" }} axisLine={false} tickLine={false} />
                <Tooltip
                  formatter={(v: number) => [`${v.toLocaleString()}đ`, "MRR"]}
                  contentStyle={{ fontSize: 11, borderRadius: 8, border: "1px solid #e2e8f0" }}
                />
                <Line
                  type="monotone"
                  dataKey="mrr"
                  stroke="#4f46e5"
                  strokeWidth={2.5}
                  dot={{ fill: "#4f46e5", r: 3, strokeWidth: 0 }}
                  activeDot={{ r: 5 }}
                />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        {/* Server resources */}
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
          <h2 className="font-semibold text-slate-900 mb-4">Server Resources</h2>
          <div className="space-y-4">
            {serverMetrics.map((m) => (
              <div key={m.label}>
                <div className="flex justify-between text-sm mb-1.5">
                  <span className="font-medium text-slate-700">{m.label}</span>
                  <span className="font-bold text-slate-900">{m.value}%</span>
                </div>
                <div className="h-2 bg-slate-100 rounded-full overflow-hidden">
                  <div
                    className={`h-full rounded-full transition-all ${m.color}`}
                    style={{ width: `${m.value}%` }}
                  />
                </div>
              </div>
            ))}
          </div>
          <div className="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-3 text-center">
            {[
              { label: "Uptime", value: "99.98%" },
              { label: "Requests/s", value: "2,841" },
              { label: "Avg latency", value: "48ms" },
            ].map((s) => (
              <div key={s.label}>
                <p className="text-sm font-bold text-slate-900">{s.value}</p>
                <p className="text-[11px] text-slate-500">{s.label}</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Quick stats row */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
        {[
          { label: "Questions in bank", value: "1,248" },
          { label: "Exams conducted", value: "3,421" },
          { label: "OCR jobs today", value: "28" },
          { label: "Support tickets", value: "7 open" },
        ].map((s) => (
          <div key={s.label} className="bg-white rounded-xl border border-slate-200 shadow-sm p-4 text-center">
            <p className="text-xl font-bold text-slate-900">{s.value}</p>
            <p className="text-xs text-slate-500 mt-1">{s.label}</p>
          </div>
        ))}
      </div>
    </div>
  );
}
