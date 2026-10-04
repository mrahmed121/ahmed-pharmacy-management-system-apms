import { useEffect, useState } from 'react';
import api from '../../../api/client';
export default function PosPage() {
  const [medicines, setMedicines] = useState([]); const [cart, setCart] = useState([]);
  const [search, setSearch] = useState(''); const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState('');
  useEffect(() => { api.get('/medicines?per_page=100').then((r) => setMedicines(r.data.data || [])).finally(() => setLoading(false)); }, []);
  const addToCart = (med) => {
    setCart((c) => { const ex = c.find((i) => i.id === med.id);
      return ex ? c.map((i) => i.id === med.id ? { ...i, qty: i.qty + 1 } : i) : [...c, { ...med, qty: 1 }]; });
  };
  const total = cart.reduce((s, i) => s + i.qty * parseFloat(i.sale_price), 0);
  const checkout = async () => {
    setMessage('');
    try {
      const res = await api.post('/pos/sales', { items: cart.map((i) => ({ medicine_id: i.id, quantity: i.qty })), payment_method: 'cash', idempotency_key: 'pos-' + Date.now() });
      setMessage(`Sale complete: ${res.data.data.invoice_number} — Rs ${res.data.data.total}`);
      setCart([]);
    } catch (e) { setMessage(e.response?.data?.message || 'Sale failed'); }
  };
  const filtered = medicines.filter((m) => m.name.toLowerCase().includes(search.toLowerCase()) || (m.barcode || '').includes(search));
  if (loading) return <div className="h-64 animate-pulse rounded-lg bg-slate-800/50" />;
  return (
    <div className="grid gap-4 lg:grid-cols-3">
      <div className="space-y-3 lg:col-span-2">
        <input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search medicine or scan barcode..." className="w-full rounded-lg bg-slate-800 px-4 py-2 text-sm text-slate-200" autoFocus />
        <div className="grid gap-2 md:grid-cols-2">
          {filtered.slice(0, 12).map((m) => (
            <button key={m.id} onClick={() => addToCart(m)} className="rounded-lg border border-slate-800 bg-slate-900 p-3 text-left transition hover:border-emerald-500/50">
              <div className="font-medium text-slate-100">💊 {m.name}</div>
              <div className="flex justify-between text-xs text-slate-500"><span>{m.generic_name}</span><span className="font-bold text-emerald-400">Rs {m.sale_price}</span></div>
              <div className="text-[10px] text-slate-600">Stock: {m.total_stock} · {m.rack_location}</div>
            </button>))}
        </div>
      </div>
      <div className="rounded-lg border border-slate-800 bg-slate-900 p-4">
        <h3 className="font-bold text-slate-100">Cart ({cart.length})</h3>
        <div className="mt-2 space-y-2">{cart.map((i) => (
          <div key={i.id} className="flex justify-between text-sm"><span className="text-slate-300">{i.name} × {i.qty}</span><span className="text-slate-100">Rs {(i.qty * parseFloat(i.sale_price)).toFixed(2)}</span></div>))}
        </div>
        <div className="mt-4 border-t border-slate-800 pt-2 flex justify-between font-bold"><span className="text-slate-300">Total</span><span className="text-emerald-400">Rs {total.toFixed(2)}</span></div>
        <button onClick={checkout} disabled={!cart.length} className="mt-3 w-full rounded-lg bg-emerald-600 py-2 font-bold text-white disabled:opacity-50">Complete Sale</button>
        {message && <div className="mt-2 text-sm text-slate-300">{message}</div>}
      </div>
    </div>
  );
}
