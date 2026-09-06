import { useState } from "react";
import { Link } from "react-router-dom";
import { ArrowLeft, Loader2, CheckCircle2 } from "lucide-react";

export default function ForgotPassword() {
  const [email, setEmail] = useState("");
  const [loading, setLoading] = useState(false);
  const [sent, setSent] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setTimeout(() => { setLoading(false); setSent(true); }, 1500);
  };

  if (sent) {
    return (
      <div className="text-center">
        <div className="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-5">
          <CheckCircle2 size={28} className="text-emerald-600" />
        </div>
        <h1 className="text-xl font-bold text-slate-900 mb-2">Check your email</h1>
        <p className="text-sm text-slate-500 mb-6 leading-relaxed">
          We sent a password reset link to <strong className="text-slate-700">{email}</strong>. It expires in 15 minutes.
        </p>
        <Link
          to="/login"
          className="inline-flex items-center gap-2 text-sm text-indigo-600 font-medium hover:text-indigo-700"
        >
          <ArrowLeft size={14} /> Back to sign in
        </Link>
      </div>
    );
  }

  return (
    <div>
      <Link to="/login" className="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
        <ArrowLeft size={14} /> Back to sign in
      </Link>
      <h1 className="text-2xl font-bold text-slate-900 mb-1">Reset your password</h1>
      <p className="text-sm text-slate-500 mb-8">Enter your email and we'll send you a reset link.</p>

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="you@example.com"
            required
            className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
          />
        </div>
        <button
          type="submit"
          disabled={loading}
          className="w-full h-10 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-lg text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2"
        >
          {loading && <Loader2 size={14} className="animate-spin" />}
          {loading ? "Sending…" : "Send reset link"}
        </button>
      </form>
    </div>
  );
}
