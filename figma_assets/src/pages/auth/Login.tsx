import { useState } from "react";
import { Link } from "react-router-dom";
import { Eye, EyeOff, Loader2 } from "lucide-react";

export default function Login() {
  const [showPw, setShowPw] = useState(false);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({ email: "", password: "", remember: false });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setTimeout(() => setLoading(false), 1500);
  };

  return (
    <div>
      <h1 className="text-2xl font-bold text-slate-900 mb-1">Welcome back</h1>
      <p className="text-sm text-slate-500 mb-8">Sign in to your OnTap account to continue.</p>

      {/* wire:submit.prevent="login" */}
      <form onSubmit={handleSubmit} className="space-y-4">
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
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
          <div className="relative">
            <input
              type={showPw ? "text" : "password"}
              value={form.password}
              onChange={(e) => setForm({ ...form, password: e.target.value })}
              placeholder="••••••••"
              required
              className="w-full h-10 px-3.5 pr-10 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
            />
            <button
              type="button"
              onClick={() => setShowPw(!showPw)}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
            >
              {showPw ? <EyeOff size={14} /> : <Eye size={14} />}
            </button>
          </div>
        </div>

        <div className="flex items-center justify-between">
          <label className="flex items-center gap-2 cursor-pointer">
            <input
              type="checkbox"
              checked={form.remember}
              onChange={(e) => setForm({ ...form, remember: e.target.checked })}
              className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
            />
            <span className="text-sm text-slate-600">Remember me</span>
          </label>
          <Link to="/forgot-password" className="text-sm text-indigo-600 font-medium hover:text-indigo-700 transition-colors">
            Forgot password?
          </Link>
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full h-10 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-lg text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2"
        >
          {loading && <Loader2 size={14} className="animate-spin" />}
          {loading ? "Signing in…" : "Sign in"}
        </button>
      </form>

      <p className="text-sm text-slate-500 text-center mt-6">
        Don't have an account?{" "}
        <Link to="/register" className="text-indigo-600 font-medium hover:text-indigo-700">
          Create one free
        </Link>
      </p>
    </div>
  );
}
