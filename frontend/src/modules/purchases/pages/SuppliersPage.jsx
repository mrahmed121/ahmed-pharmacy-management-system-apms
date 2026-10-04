import { useEffect, useState } from 'react';
import api from '../../../api/client';
export default function SuppliersPage() {
  const [suppliers, setSuppliers] = useState([]); const [loading, setLoading] = useState(true);
  useEffect(() => { api.get('/suppliers').then((r) => setSuppliers(r.data.data || [])).finally(() => setLoading(false)); }, []);
  if (loading) return <div className="h-64 animate-pulse rounded-lg bg-slate-800/50" />;
  return (
    <div className="space-y-4"><h2 className="text-xl font-bold text-slate-100">Suppliers</h2>
      <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
        {suppliers.map((s) => (
          <div key={s.id} className="rounded-lg border border-slate-800 bg-slate-900 p-4">
            <div className="font-medium text-slate-100">🏭 {s.name}</div>
            <div className="text-xs text-slate-500">{s.phone}</div>
            <div className="mt-2 text-sm text-slate-400">Balance: <span className="font-bold text-slate-200">Rs {s.balance}</span></div>
          </div>))}
      </div></div>
  );
}
