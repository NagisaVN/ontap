interface KpiCardProps {
  label: string;
  value: string | number;
  icon: React.ReactNode;
  delta?: string;
  deltaPositive?: boolean;
  color?: "indigo" | "emerald" | "blue" | "amber" | "rose";
}

const colors = {
  indigo: { bg: "bg-indigo-50", icon: "text-indigo-600", border: "border-indigo-100" },
  emerald: { bg: "bg-emerald-50", icon: "text-emerald-600", border: "border-emerald-100" },
  blue: { bg: "bg-blue-50", icon: "text-blue-600", border: "border-blue-100" },
  amber: { bg: "bg-amber-50", icon: "text-amber-600", border: "border-amber-100" },
  rose: { bg: "bg-rose-50", icon: "text-rose-600", border: "border-rose-100" },
};

export default function KpiCard({ label, value, icon, delta, deltaPositive = true, color = "indigo" }: KpiCardProps) {
  const c = colors[color];
  return (
    <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-start gap-4">
      <div className={`w-11 h-11 rounded-xl ${c.bg} border ${c.border} flex items-center justify-center shrink-0 ${c.icon}`}>
        {icon}
      </div>
      <div className="min-w-0">
        <p className="text-sm text-slate-500 font-medium truncate">{label}</p>
        <p className="text-2xl font-bold text-slate-900 mt-0.5 leading-none">{value}</p>
        {delta && (
          <p className={`text-xs font-medium mt-1.5 ${deltaPositive ? "text-emerald-600" : "text-rose-600"}`}>
            {deltaPositive ? "↑" : "↓"} {delta}
          </p>
        )}
      </div>
    </div>
  );
}
