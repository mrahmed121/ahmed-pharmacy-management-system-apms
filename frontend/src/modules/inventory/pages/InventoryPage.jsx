import { useEffect, useState } from 'react';
import api from '../../../api/client';
export default function InventoryPage() {
  const [meds, setMeds] = useState([]); const [loading, setLoading] = useState(true); const [tab, setTab] = useState('all');
  const [alerts, setAlerts] = useState({ low: [], expiry: [] });
  useEffect(() => {
    Promise.all([api.get('/medicines?per_page=100'), api.get('/medicines/low-stock'), api.get('/medicines/near-expiry?days=90')])
      .then(([m, l, e]) => { setMeds(m.data.data || []); setAlerts({ low: l.data.data || [], expiry: e.data.data || [] }); })
      .finally(() => setLoading(false));
  }, []);
  if (loading) return <div className="h-64 animate-pulse rounded-lg bg-slate-800/50" />;
  const shown = tab === 'low' ? meds.filter((m) => alerts.low.some((l) => l.id === m.id)) : meds;
  return (
    <div className="space-y-4">
      <div className="flex gap-2">
        {[['all', 'All Medicines'], ['low', `Low Stock (${alerts.low.length})`]].map(([k, l]) => (
          <button key={k} onClick={() => setTab(k)} className={`rounded-lg px-4 py-2 text-sm ${tab === k ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300'}`}>{l}</button>))}
        <span className="ml-auto text-sm text-amber-400">⚠️ {alerts.expiry.length} batches near expiry</span>
      </div>
      <div className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
        {shown.map((m) => { const low = alerts.low.some((l) => l.id === m.id);
          return (
          <div key={m.id} className={`rounded-lg border p-4 ${low ? 'border-red-500/50 bg-red-950/20' : 'border-slate-800 bg-slate-900'}`}>
            <div className="font-medium text-slate-100">💊 {m.name}</div>
            <div className="text-xs text-slate-500">{m.generic_name} · Rack {m.rack_location}</div>
            <div className="mt-2 flex justify-between text-sm">
              <span className={low ? 'text-red-400 font-bold' : 'text-slate-400'}>Stock: {m.total_stock}</span>
              <span className="text-emerald-400 font-bold">Rs {m.sale_price}</span>
            </div>
            {m.is_controlled && <span className="mt-1 inline-block rounded bg-amber-500/20 px-2 py-0.5 text-[10px] text-amber-300">CONTROLLED</span>}
          </div>); })}
      </div>
    </div>
  );
}
