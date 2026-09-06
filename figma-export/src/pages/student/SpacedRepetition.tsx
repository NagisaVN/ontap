import { useState } from "react";
import { RotateCcw } from "lucide-react";

const cards = [
  {
    front: "State Faraday's Law of Electromagnetic Induction.",
    back: "Faraday's Law states that the induced EMF in a closed loop is equal to the negative rate of change of magnetic flux through the loop:\n\nε = −dΦ_B/dt\n\nThe negative sign reflects Lenz's Law: the induced current opposes the change in flux.",
    subject: "Physics – Electromagnetism",
  },
  {
    front: "What is the relationship between electric field E and electric potential V?",
    back: "The electric field is the negative gradient of the electric potential:\n\nE = −∇V\n\nIn one dimension: E_x = −dV/dx\n\nThis means the field points from high potential to low potential.",
    subject: "Physics – Electromagnetism",
  },
  {
    front: "Define the term 'magnetic flux density' and state its SI unit.",
    back: "Magnetic flux density (B) is the amount of magnetic flux per unit area perpendicular to the field. It measures the strength of a magnetic field at a given point.\n\nSI unit: Tesla (T) = kg/(A·s²)",
    subject: "Physics – Electromagnetism",
  },
];

const ratings = [
  { label: "Hard", value: "hard", color: "bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200" },
  { label: "Good", value: "good", color: "bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-200" },
  { label: "Easy", value: "easy", color: "bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200" },
];

export default function SpacedRepetition() {
  const [index, setIndex] = useState(0);
  const [flipped, setFlipped] = useState(false);
  const [done, setDone] = useState(false);
  const [history, setHistory] = useState<{ card: number; rating: string }[]>([]);

  const card = cards[index];
  const total = 48;
  const current = index + 1;

  const handleRate = (rating: string) => {
    setHistory([...history, { card: index, rating }]);
    if (index + 1 >= cards.length) {
      setDone(true);
    } else {
      setIndex(index + 1);
      setFlipped(false);
    }
  };

  if (done) {
    return (
      <div className="p-6 max-w-xl mx-auto text-center">
        <div className="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4">
          <span className="text-3xl">🎉</span>
        </div>
        <h2 className="text-xl font-bold text-slate-900 mb-2">Session complete!</h2>
        <p className="text-sm text-slate-500 mb-6">You reviewed {cards.length} cards in this session.</p>
        <div className="grid grid-cols-3 gap-3 mb-8">
          {ratings.map((r) => (
            <div key={r.value} className={`py-3 rounded-xl border ${r.color}`}>
              <p className="text-lg font-bold">{history.filter(h => h.rating === r.value).length}</p>
              <p className="text-xs font-medium mt-0.5">{r.label}</p>
            </div>
          ))}
        </div>
        <button
          onClick={() => { setIndex(0); setFlipped(false); setDone(false); setHistory([]); }}
          className="flex items-center gap-2 mx-auto h-10 px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors"
        >
          <RotateCcw size={14} /> Start over
        </button>
      </div>
    );
  }

  return (
    <div className="p-6 max-w-xl mx-auto">
      {/* Header */}
      <div className="flex items-center justify-between mb-2">
        <h1 className="text-lg font-bold text-slate-900">Spaced Repetition</h1>
        <span className="text-xs font-medium text-slate-500">{current}/{total} cards</span>
      </div>

      {/* Progress bar */}
      <div className="h-1.5 bg-slate-100 rounded-full overflow-hidden mb-6">
        <div
          className="h-full bg-indigo-500 rounded-full transition-all duration-300"
          style={{ width: `${(current / total) * 100}%` }}
        />
      </div>

      <p className="text-xs font-medium text-slate-500 mb-4">{card.subject}</p>

      {/* Flip card */}
      <div
        className="flip-card-container w-full mb-6 cursor-pointer"
        style={{ height: 280 }}
        onClick={() => setFlipped(!flipped)}
      >
        <div className={`flip-card-inner w-full h-full ${flipped ? "flipped" : ""}`}>
          {/* Front */}
          <div className="flip-card-front w-full h-full bg-white rounded-2xl border-2 border-slate-200 shadow-sm p-8 flex flex-col items-center justify-center">
            <div className="text-xs font-semibold text-indigo-500 uppercase tracking-wider mb-4">Question</div>
            <p className="text-center text-slate-900 font-semibold text-base leading-relaxed">{card.front}</p>
            <p className="text-xs text-slate-400 mt-6">Click to reveal answer</p>
          </div>
          {/* Back */}
          <div className="flip-card-back w-full h-full bg-indigo-50 rounded-2xl border-2 border-indigo-200 shadow-sm p-8 flex flex-col items-start justify-center overflow-y-auto">
            <div className="text-xs font-semibold text-indigo-500 uppercase tracking-wider mb-4">Answer</div>
            <p className="text-slate-800 text-sm leading-relaxed whitespace-pre-line">{card.back}</p>
          </div>
        </div>
      </div>

      {/* Rating buttons — wire:click="rate('hard')" etc. */}
      {flipped ? (
        <div className="space-y-2">
          <p className="text-xs font-semibold text-slate-500 text-center uppercase tracking-wider mb-3">How well did you know this?</p>
          <div className="grid grid-cols-3 gap-3">
            {ratings.map((r) => (
              <button
                key={r.value}
                onClick={() => handleRate(r.value)}
                className={`h-11 rounded-xl border-2 text-sm font-bold transition-all ${r.color}`}
              >
                {r.label}
              </button>
            ))}
          </div>
        </div>
      ) : (
        <p className="text-center text-sm text-slate-400">Flip the card to rate your knowledge</p>
      )}
    </div>
  );
}
