import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { ChevronDown, Play, Clock } from "lucide-react";

const majors = ["Mathematics", "Physics", "Chemistry", "Biology", "Literature", "History", "Geography"];
const subjects: Record<string, string[]> = {
  Mathematics: ["Algebra", "Calculus", "Geometry", "Probability & Statistics"],
  Physics: ["Mechanics", "Thermodynamics", "Electromagnetism", "Optics", "Modern Physics"],
  Chemistry: ["Inorganic Chemistry", "Organic Chemistry", "Physical Chemistry"],
  Biology: ["Cell Biology", "Genetics", "Ecology", "Human Biology"],
  Literature: ["Vietnamese Literature", "World Literature", "Composition"],
  History: ["Vietnamese History", "World History"],
  Geography: ["Physical Geography", "Human Geography"],
};
const chuyenDe: Record<string, string[]> = {
  Algebra: ["Linear Equations", "Quadratic Equations", "Inequalities", "Sequences"],
  Calculus: ["Limits", "Derivatives", "Integral Calculus", "Differential Equations"],
  Electromagnetism: ["Electric Fields", "Magnetic Fields", "Electromagnetic Induction", "Waves"],
  "Organic Chemistry": ["Hydrocarbons", "Alcohols", "Aldehydes", "Carboxylic Acids"],
};
const timeLimits = [15, 30, 45, 60];

export default function ExamSetup() {
  const navigate = useNavigate();
  const [major, setMajor] = useState("");
  const [subject, setSubject] = useState("");
  const [chuyende, setChuyende] = useState("");
  const [count, setCount] = useState(40);
  const [time, setTime] = useState(45);

  const availableSubjects = major ? subjects[major] || [] : [];
  const availableChuyende = subject ? chuyenDe[subject] || [] : [];

  const canStart = major && subject;

  return (
    <div className="p-6 max-w-2xl mx-auto">
      <h1 className="text-xl font-bold text-slate-900 mb-1">New Exam</h1>
      <p className="text-sm text-slate-500 mb-6">Select your subject and configure the exam settings below.</p>

      <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
        {/* Major — wire:model.live="major" */}
        <div>
          <label className="block text-sm font-semibold text-slate-700 mb-2">Major (Môn học)</label>
          <div className="relative">
            <select
              value={major}
              onChange={(e) => { setMajor(e.target.value); setSubject(""); setChuyende(""); }}
              className="w-full h-10 pl-3.5 pr-10 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
            >
              <option value="">Select major…</option>
              {majors.map((m) => <option key={m} value={m}>{m}</option>)}
            </select>
            <ChevronDown size={14} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
          </div>
        </div>

        {/* Subject — wire:model.live="subject" */}
        <div>
          <label className="block text-sm font-semibold text-slate-700 mb-2">Subject (Chủ đề)</label>
          <div className="relative">
            <select
              value={subject}
              onChange={(e) => { setSubject(e.target.value); setChuyende(""); }}
              disabled={!major}
              className="w-full h-10 pl-3.5 pr-10 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <option value="">Select subject…</option>
              {availableSubjects.map((s) => <option key={s} value={s}>{s}</option>)}
            </select>
            <ChevronDown size={14} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
          </div>
        </div>

        {/* Chuyen de — wire:model.live="chuyende" */}
        <div>
          <label className="block text-sm font-semibold text-slate-700 mb-2">
            Sub-subject (Chuyên đề) <span className="font-normal text-slate-400">— optional</span>
          </label>
          <div className="relative">
            <select
              value={chuyende}
              onChange={(e) => setChuyende(e.target.value)}
              disabled={!subject}
              className="w-full h-10 pl-3.5 pr-10 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <option value="">All sub-subjects</option>
              {availableChuyende.map((c) => <option key={c} value={c}>{c}</option>)}
            </select>
            <ChevronDown size={14} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
          </div>
        </div>

        <hr className="border-slate-100" />

        {/* Question count slider */}
        <div>
          <div className="flex items-center justify-between mb-2">
            <label className="text-sm font-semibold text-slate-700">Number of questions</label>
            <span className="text-sm font-bold text-indigo-600">{count} questions</span>
          </div>
          <input
            type="range"
            min={10}
            max={80}
            step={5}
            value={count}
            onChange={(e) => setCount(Number(e.target.value))}
            className="w-full accent-indigo-600"
          />
          <div className="flex justify-between text-[10px] text-slate-400 mt-1">
            <span>10</span><span>40</span><span>80</span>
          </div>
        </div>

        {/* Time limit */}
        <div>
          <label className="block text-sm font-semibold text-slate-700 mb-2">Time limit</label>
          <div className="grid grid-cols-4 gap-2">
            {timeLimits.map((t) => (
              <button
                key={t}
                onClick={() => setTime(t)}
                className={`flex items-center justify-center gap-1.5 h-10 rounded-xl border-2 text-sm font-semibold transition-all ${
                  time === t
                    ? "border-indigo-500 bg-indigo-50 text-indigo-700"
                    : "border-slate-200 text-slate-600 hover:border-slate-300"
                }`}
              >
                <Clock size={13} />
                {t}m
              </button>
            ))}
          </div>
        </div>

        {/* Summary */}
        {canStart && (
          <div className="bg-slate-50 rounded-xl p-4 border border-slate-200">
            <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Exam Summary</p>
            <div className="space-y-1">
              <div className="flex justify-between text-sm"><span className="text-slate-600">Subject</span><span className="font-medium text-slate-900">{subject}{chuyende ? ` – ${chuyende}` : ""}</span></div>
              <div className="flex justify-between text-sm"><span className="text-slate-600">Questions</span><span className="font-medium text-slate-900">{count}</span></div>
              <div className="flex justify-between text-sm"><span className="text-slate-600">Time limit</span><span className="font-medium text-slate-900">{time} minutes</span></div>
            </div>
          </div>
        )}

        <button
          disabled={!canStart}
          onClick={() => navigate("/student/exam/room")}
          className="w-full h-11 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl text-white text-sm font-bold transition-colors flex items-center justify-center gap-2"
        >
          <Play size={16} /> Start Exam
        </button>
      </div>
    </div>
  );
}
