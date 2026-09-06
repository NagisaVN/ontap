import Badge from "../../components/ui/Badge";

const roster = [
  { name: "Tran Minh Quan", avatar: "MQ", score: 94, rank: 1, exams: 12, status: "active" },
  { name: "Le Thi Hoa", avatar: "TH", score: 88, rank: 2, exams: 11, status: "active" },
  { name: "Nguyen Thi Giang", avatar: "NG", score: 80, rank: 3, exams: 10, status: "active" },
  { name: "Pham Van Duc", avatar: "VD", score: 76, rank: 4, exams: 9, status: "active" },
  { name: "Hoang Thi Mai", avatar: "TM", score: 70, rank: 5, exams: 8, status: "inactive" },
  { name: "Dao Quang Huy", avatar: "QH", score: 65, rank: 6, exams: 7, status: "active" },
  { name: "Bui Thi Lan", avatar: "TL", score: 58, rank: 7, exams: 6, status: "active" },
];

const difficultyAnalysis = [
  { question: "Electromagnetic induction: calculate induced EMF in rotating loop", failRate: 78, difficulty: "Hard", topic: "Electromagnetism" },
  { question: "Organic synthesis: multi-step reaction pathway with stereochemistry", failRate: 71, difficulty: "Hard", topic: "Organic Chemistry" },
  { question: "Integral of trigonometric composite function with substitution", failRate: 65, difficulty: "Hard", topic: "Calculus" },
  { question: "Newton's third law application in connected body system", failRate: 52, difficulty: "Medium", topic: "Mechanics" },
  { question: "Genetic inheritance: sex-linked trait probability calculation", failRate: 48, difficulty: "Medium", topic: "Genetics" },
  { question: "Identify correct statement about Faraday's Law", failRate: 31, difficulty: "Medium", topic: "Electromagnetism" },
  { question: "SI unit of electric flux", failRate: 14, difficulty: "Easy", topic: "Electrostatics" },
];

const FAIL_THRESHOLD = 50;

export default function Reports() {
  return (
    <div className="p-6 max-w-5xl mx-auto space-y-6">
      <h1 className="text-xl font-bold text-slate-900">Class & Performance Reports</h1>

      {/* Class roster */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="px-5 py-4 border-b border-slate-100">
          <h2 className="font-semibold text-slate-900">Class Roster</h2>
          <p className="text-xs text-slate-500 mt-0.5">Physics – Electromagnetism (September 2026)</p>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-slate-100 bg-slate-50">
                {["Rank", "Student", "Avg Score", "Exams", "Status"].map((h) => (
                  <th key={h} className="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {roster.map((s) => (
                <tr key={s.name} className="hover:bg-slate-50 transition-colors">
                  <td className="px-5 py-3.5">
                    <span className={`text-sm font-bold ${s.rank <= 3 ? "text-amber-500" : "text-slate-400"}`}>
                      #{s.rank}
                    </span>
                  </td>
                  <td className="px-5 py-3.5">
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <span className="text-xs font-bold text-indigo-700">{s.avatar}</span>
                      </div>
                      <span className="font-medium text-slate-900">{s.name}</span>
                    </div>
                  </td>
                  <td className="px-5 py-3.5">
                    <div className="flex items-center gap-2">
                      <span className={`font-bold text-sm ${s.score >= 80 ? "text-emerald-600" : s.score >= 60 ? "text-amber-600" : "text-rose-600"}`}>
                        {s.score}%
                      </span>
                      <div className="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div className={`h-full rounded-full ${s.score >= 80 ? "bg-emerald-500" : s.score >= 60 ? "bg-amber-400" : "bg-rose-400"}`} style={{ width: `${s.score}%` }} />
                      </div>
                    </div>
                  </td>
                  <td className="px-5 py-3.5 text-slate-600">{s.exams}</td>
                  <td className="px-5 py-3.5">
                    <Badge variant={s.status === "active" ? "success" : "neutral"}>{s.status}</Badge>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Difficulty analysis */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
          <div>
            <h2 className="font-semibold text-slate-900">Question Difficulty Analysis</h2>
            <p className="text-xs text-slate-500 mt-0.5">Questions with high failure rates are highlighted in rose.</p>
          </div>
          <div className="flex items-center gap-2 text-xs text-rose-600 font-medium">
            <div className="w-2 h-2 rounded-full bg-rose-400" /> Above {FAIL_THRESHOLD}% fail rate
          </div>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-slate-100 bg-slate-50">
                {["Question", "Topic", "Difficulty", "Fail Rate"].map((h) => (
                  <th key={h} className="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {difficultyAnalysis.map((item, i) => {
                const isHigh = item.failRate > FAIL_THRESHOLD;
                return (
                  <tr key={i} className={`transition-colors ${isHigh ? "bg-rose-50 hover:bg-rose-100/70" : "hover:bg-slate-50"}`}>
                    <td className="px-5 py-3.5 max-w-xs">
                      <p className={`text-xs leading-relaxed ${isHigh ? "text-rose-800 font-medium" : "text-slate-800"}`}>
                        {item.question}
                      </p>
                    </td>
                    <td className="px-5 py-3.5 text-slate-600 whitespace-nowrap text-xs">{item.topic}</td>
                    <td className="px-5 py-3.5 whitespace-nowrap">
                      <Badge variant={item.difficulty === "Hard" ? "error" : item.difficulty === "Medium" ? "warning" : "success"}>
                        {item.difficulty}
                      </Badge>
                    </td>
                    <td className="px-5 py-3.5 whitespace-nowrap">
                      <div className="flex items-center gap-2">
                        <span className={`text-sm font-bold ${isHigh ? "text-rose-600" : "text-slate-700"}`}>
                          {item.failRate}%
                        </span>
                        <div className="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                          <div
                            className={`h-full rounded-full ${isHigh ? "bg-rose-400" : "bg-slate-300"}`}
                            style={{ width: `${item.failRate}%` }}
                          />
                        </div>
                      </div>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
