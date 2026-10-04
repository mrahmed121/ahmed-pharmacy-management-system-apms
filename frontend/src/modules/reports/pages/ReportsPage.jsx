import { useEffect, useState } from 'react';
import api from '../../../api/client';
export default function ReportsPage() {
  const [data, setData] = useState([]); const [loading, setLoading] = useState(true);
  useEffect(() => { api.get('/reports/daily-sales?days=14').then((r) => setData(r.data.data || [])).finally(() => setLoading(false)); }, []);
  const max = Math.max(...data.map((d) => parseFloat(d.revenue)), 1);
  if (loading) return <div className="h-64 animate-pulse rounded-lg bg-slate-800/50" />;
  return (
    <div className="space-y-4"><h2 className="text-xl font-bold text-slate-100">Daily Sales (14 days)</h2>
      <div className="rounded-lg border border-slate-800 bg-slate-900 p-4">
        <div className="flex h-48 items-end gap-1">
          {data.map((d) => (
            <div key={d.date} className="flex-1 rounded-t bg-emerald-500/70" style={{ height: `${(parseFloat(d.revenue) / max) * 100}%` }} title={`${d.date}: Rs ${d.revenue}`} />
          ))}
        </div>
        <div className="mt-2 flex justify-between text-xs text-slate-500"><span>{data[0]?.date}</span><span>{data[data.length - 1]?.date}</span></div>
      </div>
      <div className="text-sm text-slate-400">Total revenue: <span className="font-bold text-emerald-400">Rs {data.reduce((s, d) => s + parseFloat(d.revenue), 0).toFixed(2)}</span></div>
    </div>
  );
}
