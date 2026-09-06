import { useState } from "react";
import { Camera, Sun, Moon, Loader2, CheckCircle2 } from "lucide-react";

export default function Profile() {
  const [dark, setDark] = useState(false);
  const [loading, setLoading] = useState(false);
  const [saved, setSaved] = useState(false);
  const [form, setForm] = useState({ name: "Nguyen Thi Giang", email: "giang.nguyen@example.com", phone: "0901 234 567" });

  const handleSave = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setTimeout(() => { setLoading(false); setSaved(true); setTimeout(() => setSaved(false), 2000); }, 1200);
  };

  return (
    <div className="p-6 max-w-2xl mx-auto">
      <h1 className="text-xl font-bold text-slate-900 mb-6">My Profile</h1>

      {/* Avatar upload */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-5">
        <h2 className="text-sm font-semibold text-slate-700 mb-4">Profile photo</h2>
        <div className="flex items-center gap-5">
          <div className="relative shrink-0">
            <div className="w-20 h-20 rounded-2xl bg-indigo-100 flex items-center justify-center overflow-hidden">
              <span className="text-3xl font-bold text-indigo-600">NG</span>
            </div>
            <label className="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center cursor-pointer shadow">
              <Camera size={12} className="text-white" />
              <input type="file" accept="image/*" className="sr-only" />
            </label>
          </div>
          <div>
            <p className="text-sm font-medium text-slate-900">Upload a new photo</p>
            <p className="text-xs text-slate-500 mt-0.5">JPG, PNG up to 2 MB. Square image recommended.</p>
          </div>
        </div>
      </div>

      {/* Personal info */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-5">
        <h2 className="text-sm font-semibold text-slate-700 mb-4">Personal information</h2>
        <form onSubmit={handleSave} className="space-y-4">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Full name</label>
              <input
                type="text"
                value={form.name}
                onChange={(e) => setForm({ ...form, name: e.target.value })}
                className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-slate-700 mb-1.5">Phone number</label>
              <input
                type="tel"
                value={form.phone}
                onChange={(e) => setForm({ ...form, phone: e.target.value })}
                className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
              />
            </div>
          </div>
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
            <input
              type="email"
              value={form.email}
              onChange={(e) => setForm({ ...form, email: e.target.value })}
              className="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
            />
          </div>
          <div className="flex items-center gap-3 pt-2">
            <button
              type="submit"
              disabled={loading}
              className="h-9 px-5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-lg text-white text-sm font-semibold transition-colors flex items-center gap-2"
            >
              {loading && <Loader2 size={13} className="animate-spin" />}
              {loading ? "Saving…" : "Save changes"}
            </button>
            {saved && (
              <span className="flex items-center gap-1.5 text-sm text-emerald-600 font-medium">
                <CheckCircle2 size={14} /> Saved!
              </span>
            )}
          </div>
        </form>
      </div>

      {/* Appearance */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 className="text-sm font-semibold text-slate-700 mb-4">Appearance</h2>
        <div className="flex items-center justify-between">
          <div>
            <p className="text-sm font-medium text-slate-900">Theme preference</p>
            <p className="text-xs text-slate-500 mt-0.5">Choose between light and dark mode.</p>
          </div>
          <button
            onClick={() => setDark(!dark)}
            className={`relative w-14 h-7 rounded-full transition-colors duration-200 ${dark ? "bg-indigo-600" : "bg-slate-200"}`}
          >
            <span
              className={`absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white shadow flex items-center justify-center transition-transform duration-200 ${dark ? "translate-x-7" : "translate-x-0"}`}
            >
              {dark ? <Moon size={12} className="text-indigo-600" /> : <Sun size={12} className="text-amber-500" />}
            </span>
          </button>
        </div>
      </div>
    </div>
  );
}
