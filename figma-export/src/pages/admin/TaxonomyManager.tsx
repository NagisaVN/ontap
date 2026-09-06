import { useState } from "react";
import { ChevronRight, ChevronDown, Plus, Pencil, Trash2, X, Check } from "lucide-react";

interface TreeNode {
  id: number;
  name: string;
  children?: TreeNode[];
}

const initialTree: TreeNode[] = [
  {
    id: 1, name: "Natural Sciences", children: [
      { id: 11, name: "Physics", children: [
        { id: 111, name: "Mechanics" },
        { id: 112, name: "Thermodynamics" },
        { id: 113, name: "Electromagnetism" },
        { id: 114, name: "Optics" },
      ]},
      { id: 12, name: "Chemistry", children: [
        { id: 121, name: "Inorganic Chemistry" },
        { id: 122, name: "Organic Chemistry" },
        { id: 123, name: "Physical Chemistry" },
      ]},
      { id: 13, name: "Biology", children: [
        { id: 131, name: "Cell Biology" },
        { id: 132, name: "Genetics" },
        { id: 133, name: "Ecology" },
      ]},
    ]
  },
  {
    id: 2, name: "Mathematics", children: [
      { id: 21, name: "Algebra", children: [
        { id: 211, name: "Linear Equations" },
        { id: 212, name: "Quadratic Equations" },
      ]},
      { id: 22, name: "Calculus", children: [
        { id: 221, name: "Limits & Continuity" },
        { id: 222, name: "Derivatives" },
        { id: 223, name: "Integral Calculus" },
      ]},
      { id: 23, name: "Geometry" },
    ]
  },
  {
    id: 3, name: "Social Sciences", children: [
      { id: 31, name: "History", children: [
        { id: 311, name: "Vietnamese History" },
        { id: 312, name: "World History" },
      ]},
      { id: 32, name: "Geography" },
      { id: 33, name: "Literature" },
    ]
  },
];

interface NodeProps {
  node: TreeNode;
  depth: number;
}

function TreeNodeRow({ node, depth }: NodeProps) {
  const [open, setOpen] = useState(depth === 0);
  const [addingChild, setAddingChild] = useState(false);
  const [newName, setNewName] = useState("");
  const hasChildren = node.children && node.children.length > 0;

  return (
    <div>
      <div
        className={`flex items-center gap-1 py-1.5 rounded-lg hover:bg-slate-50 group transition-colors ${depth === 0 ? "mb-0.5" : ""}`}
        style={{ paddingLeft: `${depth * 20 + 8}px` }}
      >
        {/* Toggle */}
        <button
          onClick={() => setOpen(!open)}
          className={`w-5 h-5 flex items-center justify-center rounded text-slate-400 transition-colors ${hasChildren ? "hover:bg-slate-200" : "invisible"}`}
        >
          {open ? <ChevronDown size={13} /> : <ChevronRight size={13} />}
        </button>

        {/* Name */}
        <span className={`flex-1 text-sm ${depth === 0 ? "font-bold text-slate-900" : depth === 1 ? "font-semibold text-slate-800" : "font-medium text-slate-700"}`}>
          {node.name}
        </span>

        {/* Actions (visible on hover) */}
        <div className="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
          <button
            onClick={() => setAddingChild(true)}
            className="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
            title="Add child"
          >
            <Plus size={11} />
          </button>
          <button className="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-colors" title="Edit">
            <Pencil size={11} />
          </button>
          {depth > 0 && (
            <button className="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Delete">
              <Trash2 size={11} />
            </button>
          )}
        </div>
      </div>

      {/* Add child inline form */}
      {addingChild && (
        <div
          className="flex items-center gap-2 py-1.5 pr-2"
          style={{ paddingLeft: `${(depth + 1) * 20 + 8}px` }}
        >
          <div className="w-5 shrink-0" />
          <input
            autoFocus
            value={newName}
            onChange={(e) => setNewName(e.target.value)}
            placeholder="New node name…"
            onKeyDown={(e) => { if (e.key === "Enter") { setAddingChild(false); setNewName(""); } if (e.key === "Escape") { setAddingChild(false); setNewName(""); } }}
            className="flex-1 h-7 px-2 border border-indigo-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/30 bg-indigo-50"
          />
          <button onClick={() => { setAddingChild(false); setNewName(""); }} className="w-6 h-6 flex items-center justify-center text-emerald-600 hover:bg-emerald-50 rounded transition-colors">
            <Check size={12} />
          </button>
          <button onClick={() => { setAddingChild(false); setNewName(""); }} className="w-6 h-6 flex items-center justify-center text-slate-400 hover:bg-slate-100 rounded transition-colors">
            <X size={12} />
          </button>
        </div>
      )}

      {/* Children */}
      {open && hasChildren && (
        <div>
          {node.children!.map((child) => (
            <TreeNodeRow key={child.id} node={child} depth={depth + 1} />
          ))}
        </div>
      )}
    </div>
  );
}

export default function TaxonomyManager() {
  return (
    <div className="p-6 max-w-3xl mx-auto">
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-xl font-bold text-slate-900">Taxonomy Manager</h1>
          <p className="text-sm text-slate-500 mt-0.5">Manage the Major → Subject → Sub-subject hierarchy.</p>
        </div>
        <button className="flex items-center gap-2 h-9 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white text-sm font-semibold transition-colors">
          <Plus size={14} /> Add Major
        </button>
      </div>

      <div className="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        {initialTree.map((node) => (
          <TreeNodeRow key={node.id} node={node} depth={0} />
        ))}
      </div>

      {/* Legend */}
      <div className="mt-4 flex items-center gap-4 text-xs text-slate-400">
        <div className="flex items-center gap-1.5"><span className="font-bold text-slate-600">Bold</span> = Major</div>
        <div className="flex items-center gap-1.5"><span className="font-semibold text-slate-500">Semibold</span> = Subject</div>
        <div className="flex items-center gap-1.5"><span className="text-slate-500">Regular</span> = Sub-subject</div>
      </div>
    </div>
  );
}
