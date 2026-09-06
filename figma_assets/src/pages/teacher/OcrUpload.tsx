import { useState, useRef } from "react";
import { Upload, ScanLine, Loader2, CheckCircle2, FileImage, X } from "lucide-react";

const mockExtracted = `Question 1: A body moving with velocity 20 m/s decelerates uniformly at 4 m/s² until it stops. How far does it travel before stopping?

A) 25 m   B) 50 m   C) 100 m   D) 150 m

Question 2: The force of gravity between two objects is greatest when:

A) The masses are small and the distance is large
B) The masses are large and the distance is small
C) Both masses and distance are large
D) Both masses and distance are small

Question 3: Which of the following is NOT a unit of energy?

A) Joule   B) Calorie   C) Newton   D) Kilowatt-hour`;

const parsedQuestions = [
  { id: 1, text: "A body moving with velocity 20 m/s decelerates uniformly at 4 m/s² until it stops. How far does it travel before stopping?", options: ["25 m", "50 m", "100 m", "150 m"], correct: "B" },
  { id: 2, text: "The force of gravity between two objects is greatest when:", options: ["The masses are small and the distance is large", "The masses are large and the distance is small", "Both masses and distance are large", "Both masses and distance are small"], correct: "B" },
  { id: 3, text: "Which of the following is NOT a unit of energy?", options: ["Joule", "Calorie", "Newton", "Kilowatt-hour"], correct: "C" },
];

export default function OcrUpload() {
  const [dragging, setDragging] = useState(false);
  const [file, setFile] = useState<File | null>(null);
  const [extractedText, setExtractedText] = useState("");
  const [parsing, setParsing] = useState(false);
  const [parsed, setParsed] = useState(false);
  const fileRef = useRef<HTMLInputElement>(null);

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setDragging(false);
    const f = e.dataTransfer.files[0];
    if (f) handleFile(f);
  };

  const handleFile = (f: File) => {
    setFile(f);
    setParsed(false);
    // Simulate OCR extraction
    setTimeout(() => setExtractedText(mockExtracted), 800);
  };

  const handleParse = () => {
    setParsing(true);
    setTimeout(() => { setParsing(false); setParsed(true); }, 1800);
  };

  return (
    <div className="p-6 max-w-5xl mx-auto">
      <h1 className="text-xl font-bold text-slate-900 mb-1">OCR Question Importer</h1>
      <p className="text-sm text-slate-500 mb-6">Upload a scanned document or image. We extract the text and parse it into multiple-choice questions.</p>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {/* Left — Upload + preview */}
        <div className="space-y-4">
          {/* Drop zone */}
          <div
            onDragOver={(e) => { e.preventDefault(); setDragging(true); }}
            onDragLeave={() => setDragging(false)}
            onDrop={handleDrop}
            onClick={() => fileRef.current?.click()}
            className={`border-2 border-dashed rounded-xl p-8 flex flex-col items-center gap-3 cursor-pointer transition-colors ${
              dragging ? "border-indigo-400 bg-indigo-50" : "border-slate-300 hover:border-indigo-300 hover:bg-slate-50"
            }`}
          >
            <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${dragging ? "bg-indigo-100" : "bg-slate-100"}`}>
              <Upload size={20} className={dragging ? "text-indigo-600" : "text-slate-400"} />
            </div>
            <div className="text-center">
              <p className="text-sm font-semibold text-slate-700">
                {dragging ? "Release to upload" : "Drop image or PDF here"}
              </p>
              <p className="text-xs text-slate-500 mt-1">or click to browse &mdash; JPG, PNG, PDF up to 20 MB</p>
            </div>
            <input
              ref={fileRef}
              type="file"
              accept=".jpg,.jpeg,.png,.pdf"
              className="sr-only"
              onChange={(e) => { if (e.target.files?.[0]) handleFile(e.target.files[0]); }}
            />
          </div>

          {/* File preview */}
          {file && (
            <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
              <div className="flex items-center gap-3 mb-3">
                <div className="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                  <FileImage size={16} className="text-indigo-600" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-semibold text-slate-900 truncate">{file.name}</p>
                  <p className="text-xs text-slate-500">{(file.size / 1024).toFixed(1)} KB</p>
                </div>
                <button onClick={() => { setFile(null); setExtractedText(""); setParsed(false); }} className="text-slate-400 hover:text-rose-500 transition-colors">
                  <X size={14} />
                </button>
              </div>
              {/* Mock image preview */}
              <div className="w-full h-48 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200">
                <div className="text-center">
                  <ScanLine size={32} className="text-slate-300 mx-auto mb-2" />
                  <p className="text-xs text-slate-400">Document preview</p>
                  {!extractedText && <p className="text-xs text-indigo-500 mt-1 animate-pulse">Extracting text…</p>}
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Right — Extracted text + actions */}
        <div className="space-y-4">
          <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <label className="block text-sm font-semibold text-slate-700 mb-2">
              Extracted text <span className="font-normal text-slate-400">— editable</span>
            </label>
            <textarea
              value={extractedText}
              onChange={(e) => setExtractedText(e.target.value)}
              rows={12}
              placeholder="OCR extracted text will appear here once you upload a file. You can edit it before parsing."
              className="w-full px-3.5 py-3 border border-slate-200 rounded-lg text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 resize-none transition-colors font-mono leading-relaxed"
            />
          </div>

          <button
            disabled={!extractedText || parsing}
            onClick={handleParse}
            className="w-full h-10 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl text-white text-sm font-bold transition-colors flex items-center justify-center gap-2"
          >
            {parsing ? <><Loader2 size={14} className="animate-spin" /> Parsing…</> : <><ScanLine size={14} /> Auto-parse to MCQs</>}
          </button>

          {/* Parsed result */}
          {parsed && (
            <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
              <div className="flex items-center gap-2 px-5 py-3 border-b border-slate-100 bg-emerald-50">
                <CheckCircle2 size={14} className="text-emerald-600" />
                <p className="text-sm font-semibold text-emerald-700">{parsedQuestions.length} questions extracted</p>
              </div>
              <div className="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                {parsedQuestions.map((q) => (
                  <div key={q.id} className="px-5 py-3.5">
                    <p className="text-xs font-semibold text-slate-500 mb-1">Q{q.id}</p>
                    <p className="text-sm text-slate-800 leading-snug mb-2">{q.text}</p>
                    <div className="grid grid-cols-2 gap-1">
                      {q.options.map((opt, i) => (
                        <span key={i} className={`text-xs px-2 py-1 rounded-lg ${String.fromCharCode(65 + i) === q.correct ? "bg-emerald-50 text-emerald-700 font-semibold" : "bg-slate-50 text-slate-600"}`}>
                          {String.fromCharCode(65 + i)}. {opt}
                        </span>
                      ))}
                    </div>
                  </div>
                ))}
              </div>
              <div className="px-5 py-3 border-t border-slate-100">
                <button className="w-full h-9 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white text-sm font-bold transition-colors">
                  Import all to Question Bank
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
