import { useState } from "react";
import { Trophy, Star, Lock, CheckCircle2, CreditCard, X } from "lucide-react";
import Badge from "../../components/ui/Badge";

const weeklyLeaders = [
  { rank: 1, name: "Tran Minh Quan", score: 4820, avatar: "MQ", badge: "🥇" },
  { rank: 2, name: "Le Thi Hoa", score: 4610, avatar: "TH", badge: "🥈" },
  { rank: 3, name: "Nguyen Thi Giang", score: 4340, avatar: "NG", badge: "🥉" },
  { rank: 4, name: "Pham Van Duc", score: 4120, avatar: "VD" },
  { rank: 5, name: "Hoang Thi Mai", score: 3980, avatar: "TM" },
  { rank: 6, name: "Dao Quang Huy", score: 3740, avatar: "QH" },
  { rank: 7, name: "Bui Thi Lan", score: 3510, avatar: "TL" },
  { rank: 8, name: "Vo Minh Tri", score: 3290, avatar: "MT" },
];

const achievements = [
  { icon: "🔥", label: "7-Day Streak", unlocked: true },
  { icon: "⚡", label: "Speed Demon", unlocked: true },
  { icon: "🎯", label: "Perfect Score", unlocked: true },
  { icon: "📚", label: "Knowledge Seeker", unlocked: true },
  { icon: "🏆", label: "Top 3 Weekly", unlocked: false },
  { icon: "💎", label: "Pro Member", unlocked: false },
  { icon: "🌟", label: "100 Exams", unlocked: false },
  { icon: "🚀", label: "On Fire", unlocked: false },
  { icon: "👑", label: "Champion", unlocked: false },
  { icon: "🎓", label: "Scholar", unlocked: false },
  { icon: "⭐", label: "Star Student", unlocked: false },
  { icon: "🔮", label: "Fortune Teller", unlocked: false },
];

const plans = [
  {
    name: "Free",
    price: "0",
    period: "forever",
    color: "border-slate-200",
    cta: "Current plan",
    ctaClass: "bg-slate-100 text-slate-500 cursor-default",
    features: ["20 questions/day", "Basic subjects only", "No analytics", "Community support"],
    current: true,
  },
  {
    name: "Pro",
    price: "99,000",
    period: "/month",
    color: "border-indigo-500 ring-2 ring-indigo-500/20",
    cta: "Upgrade to Pro",
    ctaClass: "bg-indigo-600 hover:bg-indigo-700 text-white",
    features: ["Unlimited questions", "All subjects", "Full analytics", "Spaced repetition", "Priority support"],
    badge: "Most popular",
    current: false,
  },
  {
    name: "VIP",
    price: "199,000",
    period: "/month",
    color: "border-amber-300",
    cta: "Upgrade to VIP",
    ctaClass: "bg-amber-500 hover:bg-amber-600 text-white",
    features: ["Everything in Pro", "OCR question uploads", "Personal tutor chat", "Exam paper downloads", "VIP leaderboard"],
    badge: "Best value",
    current: false,
  },
];

export default function Leaderboard() {
  const [tab, setTab] = useState<"week" | "all">("week");
  const [payModalPlan, setPayModalPlan] = useState<string | null>(null);
  const myRank = 3;

  return (
    <div className="p-6 max-w-4xl mx-auto space-y-6">
      <h1 className="text-xl font-bold text-slate-900">Leaderboard & Rewards</h1>

      {/* Tabs */}
      <div className="flex gap-1 bg-slate-100 rounded-xl p-1 w-fit">
        {[{ label: "This Week", value: "week" as const }, { label: "All Time", value: "all" as const }].map((t) => (
          <button
            key={t.value}
            onClick={() => setTab(t.value)}
            className={`px-4 h-8 rounded-lg text-sm font-semibold transition-all ${
              tab === t.value ? "bg-white text-slate-900 shadow-sm" : "text-slate-500 hover:text-slate-700"
            }`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {/* Leaderboard table */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
          <Trophy size={16} className="text-amber-500" />
          <h2 className="font-semibold text-slate-900">{tab === "week" ? "Weekly" : "All-Time"} XP Rankings</h2>
        </div>
        <div className="divide-y divide-slate-100">
          {weeklyLeaders.map((user) => (
            <div
              key={user.rank}
              className={`flex items-center gap-4 px-5 py-3.5 transition-colors ${
                user.rank === myRank ? "bg-indigo-50" : "hover:bg-slate-50"
              }`}
            >
              <span className="w-6 text-sm font-bold text-slate-400 shrink-0">
                {user.badge || `#${user.rank}`}
              </span>
              <div className="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                <span className="text-xs font-bold text-indigo-700">{user.avatar}</span>
              </div>
              <div className="flex-1 min-w-0">
                <p className={`text-sm font-semibold ${user.rank === myRank ? "text-indigo-700" : "text-slate-900"}`}>
                  {user.name} {user.rank === myRank && <span className="text-xs text-indigo-400 font-normal">(You)</span>}
                </p>
              </div>
              <span className="text-sm font-bold text-slate-900">{user.score.toLocaleString()} XP</span>
            </div>
          ))}
        </div>
      </div>

      {/* Achievements */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <div className="flex items-center gap-2 mb-4">
          <Star size={16} className="text-amber-500" />
          <h2 className="font-semibold text-slate-900">Achievements</h2>
          <span className="ml-auto text-xs text-slate-500">4/12 unlocked</span>
        </div>
        <div className="grid grid-cols-4 sm:grid-cols-6 gap-3">
          {achievements.map((a) => (
            <div
              key={a.label}
              title={a.label}
              className={`flex flex-col items-center gap-1.5 p-3 rounded-xl border transition-colors ${
                a.unlocked ? "border-amber-200 bg-amber-50" : "border-slate-200 bg-slate-50 opacity-60"
              }`}
            >
              <span className="text-2xl">{a.unlocked ? a.icon : "🔒"}</span>
              <span className={`text-[10px] font-medium text-center leading-tight ${a.unlocked ? "text-amber-700" : "text-slate-500"}`}>
                {a.label}
              </span>
            </div>
          ))}
        </div>
      </div>

      {/* Pricing */}
      <div>
        <h2 className="font-semibold text-slate-900 mb-4">Subscription Plans</h2>
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {plans.map((plan) => (
            <div key={plan.name} className={`bg-white rounded-xl border-2 ${plan.color} shadow-sm p-5 flex flex-col relative`}>
              {plan.badge && (
                <span className="absolute -top-3 left-1/2 -translate-x-1/2 text-xs font-bold px-3 py-1 rounded-full bg-indigo-600 text-white whitespace-nowrap">
                  {plan.badge}
                </span>
              )}
              <h3 className="font-bold text-slate-900 text-lg">{plan.name}</h3>
              <div className="flex items-baseline gap-1 mt-1 mb-4">
                {plan.price === "0" ? (
                  <span className="text-2xl font-bold text-slate-900">Free</span>
                ) : (
                  <>
                    <span className="text-2xl font-bold text-slate-900">{plan.price}đ</span>
                    <span className="text-xs text-slate-500">{plan.period}</span>
                  </>
                )}
              </div>
              <ul className="space-y-2 mb-5 flex-1">
                {plan.features.map((f) => (
                  <li key={f} className="flex items-center gap-2 text-sm text-slate-600">
                    {plan.current && plan.features.indexOf(f) > 1 ? (
                      <Lock size={12} className="text-slate-300 shrink-0" />
                    ) : (
                      <CheckCircle2 size={12} className="text-emerald-500 shrink-0" />
                    )}
                    {f}
                  </li>
                ))}
              </ul>
              <button
                disabled={plan.current}
                onClick={() => !plan.current && setPayModalPlan(plan.name)}
                className={`w-full h-9 rounded-xl text-sm font-semibold transition-colors ${plan.ctaClass}`}
              >
                {plan.cta}
              </button>
            </div>
          ))}
        </div>
      </div>

      {/* Payment modal */}
      {payModalPlan && (
        <div className="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl shadow-md w-full max-w-sm">
            <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100">
              <div className="flex items-center gap-2">
                <CreditCard size={16} className="text-indigo-600" />
                <h3 className="font-semibold text-slate-900">Upgrade to {payModalPlan}</h3>
              </div>
              <button onClick={() => setPayModalPlan(null)} className="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">
                <X size={14} />
              </button>
            </div>
            <div className="px-6 py-5 space-y-4">
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-1.5">Card number</label>
                <input placeholder="4242 4242 4242 4242" className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400" />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1.5">Expiry</label>
                  <input placeholder="MM / YY" className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1.5">CVC</label>
                  <input placeholder="•••" className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400" />
                </div>
              </div>
              <button
                onClick={() => setPayModalPlan(null)}
                className="w-full h-10 bg-indigo-600 hover:bg-indigo-700 rounded-xl text-white text-sm font-bold transition-colors"
              >
                Pay {payModalPlan === "Pro" ? "99,000đ" : "199,000đ"}
              </button>
              <p className="text-center text-xs text-slate-400">Secured by Stripe. Cancel anytime.</p>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
