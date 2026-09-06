import { AlertTriangle } from "lucide-react";
import Modal from "./Modal";

interface ConfirmModalProps {
  open: boolean;
  onClose: () => void;
  onConfirm: () => void;
  title?: string;
  message?: string;
  confirmLabel?: string;
  danger?: boolean;
}

export default function ConfirmModal({
  open,
  onClose,
  onConfirm,
  title = "Are you sure?",
  message = "This action cannot be undone.",
  confirmLabel = "Confirm",
  danger = true,
}: ConfirmModalProps) {
  return (
    <Modal open={open} onClose={onClose} title={title} size="sm">
      <div className="flex items-start gap-4">
        <div className="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
          <AlertTriangle size={18} className="text-rose-600" />
        </div>
        <p className="text-sm text-slate-600 leading-relaxed">{message}</p>
      </div>
      <div className="flex gap-3 mt-6">
        <button
          onClick={onClose}
          className="flex-1 h-9 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors"
        >
          Cancel
        </button>
        <button
          onClick={() => { onConfirm(); onClose(); }}
          className={`flex-1 h-9 rounded-lg text-sm font-semibold text-white transition-colors ${
            danger ? "bg-rose-600 hover:bg-rose-700" : "bg-indigo-600 hover:bg-indigo-700"
          }`}
        >
          {confirmLabel}
        </button>
      </div>
    </Modal>
  );
}
