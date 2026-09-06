import { useState } from "react";
import { Link } from "react-router-dom";
import { Loader2, GraduationCap, BookOpen } from "lucide-react";

export default function Register() {
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({ name: "", email: "", password: "", confirm: "", role: "student" });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setTimeout(() => setLoading(false), 1500);
  };

  return (
    <div>
      <h1 className="text-2xl font-bold text-slate-900 mb-1">Create your account</h1>
      <p className="text-sm text-slate-500 mb-8">Join thousands of students preparing smarter.</p>

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-1.5">Full name</label>
          <input
            type="text"
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
            placeholder="Nguyen Van A"
            required
            className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
          <input
            type="email"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
            placeholder="you@example.com"
            required
            className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
          />
        </div>

        <div className="grid grid-cols-2 gap-3">
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <input
              type="password"
              value={form.password}
              onChange={(e) => setForm({ ...form, password: e.target.value })}
              placeholder="••••••••"
              required
              className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Confirm</label>
            <input
              type="password"
              value={form.confirm}
              onChange={(e) => setForm({ ...form, confirm: e.target.value })}
              placeholder="••••••••"
              required
              className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
            />
          </div>
        </div>

        {/* Role selector — wire:model="role" */}
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-2">I am a…</label>
          <div className="grid grid-cols-2 gap-3">
            {[
              { value: "student", label: "Student", icon: <GraduationCap size={18} /> },
              { value: "teacher", label: "Teacher", icon: <BookOpen size={18} /> },
            ].map((r) => (
              <button
                key={r.value}
                type="button"
                onClick={() => setForm({ ...form, role: r.value })}
                className={`flex items-center gap-3 h-12 px-4 rounded-xl border-2 text-sm font-medium transition-all ${
                  form.role === r.value
                    ? "border-indigo-500 bg-indigo-50 text-indigo-700"
                    : "border-slate-200 text-slate-600 hover:border-slate-300"
                }`}
              >
                <span className={form.role === r.value ? "text-indigo-600" : "text-slate-400"}>
                  {r.icon}
                </span>
                {r.label}
              </button>
            ))}
          </div>
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full h-10 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-lg text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2 mt-2"
        >
          {loading && <Loader2 size={14} className="animate-spin" />}
          {loading ? "Creating account…" : "Create account"}
        </button>
      </form>

      <p className="text-sm text-slate-500 text-center mt-6">
        Already have an account?{" "}
        <Link to="/login" className="text-indigo-600 font-medium hover:text-indigo-700">
          Sign in
        </Link>
      </p>
    </div>
  );
}
