import { Outlet } from "react-router-dom";
import { GraduationCap, BookOpen, Award, TrendingUp } from "lucide-react";

export default function GuestLayout() {
  return (
    <div className="min-h-full flex bg-slate-50 font-sans">
      {/* Left decorative panel */}
      <div className="hidden lg:flex w-[480px] shrink-0 bg-indigo-600 flex-col p-12 relative overflow-hidden">
        {/* Background pattern */}
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-16 left-16 w-48 h-48 rounded-full bg-white" />
          <div className="absolute bottom-32 right-8 w-64 h-64 rounded-full bg-white" />
          <div className="absolute top-1/2 left-1/3 w-32 h-32 rounded-full bg-white" />
        </div>

        {/* Logo */}
        <div className="flex items-center gap-3 relative">
          <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
            <GraduationCap size={22} className="text-white" />
          </div>
          <span className="font-bold text-white text-2xl tracking-tight">OnTap</span>
        </div>

        {/* Tagline */}
        <div className="relative mt-auto">
          <h2 className="text-3xl font-bold text-white leading-tight mb-4">
            Ace every exam<br />with confidence
          </h2>
          <p className="text-indigo-200 text-sm leading-relaxed mb-10">
            OnTap combines spaced repetition, adaptive exams, and in-depth analytics to help Vietnamese students master their subjects.
          </p>

          {/* Feature pills */}
          <div className="flex flex-col gap-3">
            {[
              { icon: <BookOpen size={14} />, label: "10,000+ curated exam questions" },
              { icon: <Award size={14} />, label: "Spaced repetition flashcards" },
              { icon: <TrendingUp size={14} />, label: "Personal performance analytics" },
            ].map((f) => (
              <div key={f.label} className="flex items-center gap-3">
                <div className="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center text-white shrink-0">
                  {f.icon}
                </div>
                <span className="text-sm text-indigo-100">{f.label}</span>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Right auth form */}
      <div className="flex-1 flex items-center justify-center p-6">
        <div className="w-full max-w-md">
          {/* Mobile logo */}
          <div className="flex items-center gap-2 mb-8 lg:hidden">
            <div className="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
              <GraduationCap size={16} className="text-white" />
            </div>
            <span className="font-bold text-slate-900 text-xl tracking-tight">OnTap</span>
          </div>

          <Outlet />
        </div>
      </div>
    </div>
  );
}
