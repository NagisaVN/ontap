import { useState } from "react";
import { Plus, Trash2, CheckCircle2, ChevronDown, Loader2 } from "lucide-react";

const subjects = ["Mathematics", "Physics", "Chemistry", "Biology", "Literature"];
const difficulties = ["Easy", "Medium", "Hard"];

export default function QuestionCreator() {
  const [type, setType] = useState<"mcq" | "tf">("mcq");
  const [question, setQuestion] = useState("");
  const [subject, setSubject] = useState("");
  const [difficulty, setDifficulty] = useState("Medium");
  const [options, setOptions] = useState(["", "", "", ""]);
  const [correct, setCorrect] = useState(0);
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);

  const handleSave = () => {
    setSaving(true);
    setTimeout(() => { setSaving(false); setSaved(true); setTimeout(() => setSaved(false), 2000); }, 1200);
  };

  const updateOption = (i: number, val: string) => {
    const next = [...options];
    next[i] = val;
    setOptions(next);
  };

  const addOption = () => setOptions([...options, ""]);
  const removeOption = (i: number) => {
    if (options.length <= 2) return;
    const next = options.filter((_, idx) => idx !== i);
    setOptions(next);
    if (correct >= next.length) setCorrect(next.length - 1);
  };

  // Simple LaTeX-style preview (replace $...$ with italic)
  const preview = question.replace(/\$([^$]+)\$/g, "<em>$1</em>").replace(/\n/g, "<br/>");

  return (
    <div className="p-6 max-w-4xl mx-auto">
      <h1 className="text-xl font-bold text-slate-900 mb-1">New Question</h1>
      <p className="text-sm text-slate-500 mb-6">Use $...$ for inline math. Wrap display math with $$..$$.</p>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {/* Editor */}
        <div className="space-y-4">
          {/* Type toggle */}
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <label className="block text-sm font-semibold text-slate-700 mb-2">Question type</label>
            <div className="flex gap-2">
              {[{ val: "mcq", label: "Multiple Choice" }, { val: "tf", label: "True / False" }].map((t) => (
                <button
                  key={t.val}
                  onClick={() => setType(t.val as "mcq" | "tf")}
                  className={`flex-1 h-9 rounded-lg border-2 text-sm font-semibold transition-all ${
                    type === t.val ? "border-indigo-500 bg-indigo-50 text-indigo-700" : "border-slate-200 text-slate-600 hover:border-slate-300"
                  }`}
                >
                  {t.label}
                </button>
              ))}
            </div>
          </div>

          {/* Question text */}
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <label className="block text-sm font-semibold text-slate-700 mb-2">Question text</label>
            <textarea
              value={question}
              onChange={(e) => setQuestion(e.target.value)}
              rows={5}
              placeholder="Type the question here. Use $f(x) = x^2$ for inline math…"
              className="w-full px-3.5 py-3 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 resize-none transition-colors font-mono"
            />
          </div>

          {/* Options */}
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div className="flex items-center justify-between mb-3">
              <label className="text-sm font-semibold text-slate-700">Answer options</label>
              {type === "mcq" && (
                <button onClick={addOption} className="flex items-center gap-1 text-xs text-indigo-600 font-medium hover:text-indigo-700">
                  <Plus size={12} /> Add option
                </button>
              )}
            </div>
            <div className="space-y-2">
              {(type === "tf" ? ["True", "False"] : options).map((opt, i) => (
                <div key={i} className="flex items-center gap-2">
                  <button
                    onClick={() => setCorrect(i)}
                    className={`w-6 h-6 rounded-full border-2 shrink-0 flex items-center justify-center transition-colors ${
                      correct === i ? "border-indigo-500 bg-indigo-500" : "border-slate-300"
                    }`}
                  >
                    {correct === i && <CheckCircle2 size={13} className="text-white" />}
                  </button>
                  <span className="w-5 text-xs font-bold text-slate-400">{String.fromCharCode(65 + i)}</span>
                  {type === "tf" ? (
                    <div className="flex-1 h-9 px-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center text-sm text-slate-700">{opt}</div>
                  ) : (
                    <input
                      value={opt}
                      onChange={(e) => updateOption(i, e.target.value)}
                      placeholder={`Option ${String.fromCharCode(65 + i)}`}
                      className="flex-1 h-9 px-3 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
                    />
                  )}
                  {type === "mcq" && options.length > 2 && (
                    <button onClick={() => removeOption(i)} className="text-slate-300 hover:text-rose-500 transition-colors">
                      <Trash2 size={13} />
                    </button>
                  )}
                </div>
              ))}
            </div>
          </div>

          {/* Meta */}
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5 grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Subject</label>
              <div className="relative">
                <select value={subject} onChange={(e) => setSubject(e.target.value)} className="w-full h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                  <option value="">Select…</option>
                  {subjects.map((s) => <option key={s}>{s}</option>)}
                </select>
                <ChevronDown size={12} className="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
              </div>
            </div>
            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-1.5">Difficulty</label>
              <div className="relative">
                <select value={difficulty} onChange={(e) => setDifficulty(e.target.value)} className="w-full h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                  {difficulties.map((d) => <option key={d}>{d}</option>)}
                </select>
                <ChevronDown size={12} className="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
              </div>
            </div>
          </div>

          {/* Actions */}
          <div className="flex gap-3">
            <button
              onClick={handleSave}
              disabled={saving}
              className="flex-1 h-10 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-xl text-white text-sm font-bold transition-colors flex items-center justify-center gap-2"
            >
              {saving && <Loader2 size={13} className="animate-spin" />}
              {saving ? "Publishing…" : "Publish"}
            </button>
            <button className="flex-1 h-10 border border-slate-200 rounded-xl text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
              Save draft
            </button>
          </div>
          {saved && <p className="text-sm text-emerald-600 font-medium text-center">Question published successfully!</p>}
        </div>

        {/* Preview */}
        <div className="space-y-4">
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sticky top-6">
            <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Live Preview</p>
            <div className="bg-slate-50 rounded-xl p-4 border border-slate-200 min-h-24 mb-4">
              {question ? (
                <p className="text-sm text-slate-900 leading-relaxed" dangerouslySetInnerHTML={{ __html: preview }} />
              ) : (
                <p className="text-sm text-slate-400 italic">Question preview will appear here…</p>
              )}
            </div>
            <div className="space-y-2">
              {(type === "tf" ? ["True", "False"] : options).map((opt, i) => (
                <div
                  key={i}
                  className={`flex items-center gap-3 p-3 rounded-xl border-2 ${
                    correct === i ? "border-indigo-400 bg-indigo-50" : "border-slate-200"
                  }`}
                >
                  <span className={`w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 ${
                    correct === i ? "bg-indigo-500 text-white" : "bg-slate-200 text-slate-500"
                  }`}>
                    {String.fromCharCode(65 + i)}
                  </span>
                  <span className={`text-sm ${correct === i ? "text-indigo-800 font-medium" : "text-slate-700"}`}>
                    {opt || <span className="text-slate-300 italic">Option {String.fromCharCode(65 + i)}</span>}
                  </span>
                  {correct === i && <CheckCircle2 size={14} className="text-indigo-500 ml-auto shrink-0" />}
                </div>
              ))}
            </div>
            {subject && (
              <div className="flex items-center gap-2 mt-4 pt-4 border-t border-slate-100">
                <span className="text-xs text-slate-500">Subject:</span>
                <span className="text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">{subject}</span>
                <span className="text-xs text-slate-500 ml-auto">{difficulty}</span>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
