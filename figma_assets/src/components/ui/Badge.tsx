interface BadgeProps {
  variant?: "success" | "error" | "warning" | "info" | "neutral" | "primary";
  children: React.ReactNode;
  className?: string;
}

const variants = {
  success: "bg-emerald-50 text-emerald-700 border-emerald-200",
  error: "bg-rose-50 text-rose-700 border-rose-200",
  warning: "bg-amber-50 text-amber-700 border-amber-200",
  info: "bg-blue-50 text-blue-700 border-blue-200",
  neutral: "bg-slate-100 text-slate-600 border-slate-200",
  primary: "bg-indigo-50 text-indigo-700 border-indigo-200",
};

export default function Badge({ variant = "neutral", children, className = "" }: BadgeProps) {
  return (
    <span
      className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border ${variants[variant]} ${className}`}
    >
      {children}
    </span>
  );
}
