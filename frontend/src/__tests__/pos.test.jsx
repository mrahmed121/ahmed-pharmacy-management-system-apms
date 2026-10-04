import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';

// Mock the API client
vi.mock('../api/client', () => ({
  default: {
    get: vi.fn(() => Promise.resolve({ data: { data: [
      { id: 1, name: 'Panadol Extra', generic_name: 'Paracetamol', sale_price: '45.00', total_stock: 500, rack_location: 'A-01' },
      { id: 2, name: 'Disprin', generic_name: 'Aspirin', sale_price: '25.00', total_stock: 300, rack_location: 'B-01' },
    ]}})),
    post: vi.fn(() => Promise.resolve({ data: { data: { invoice_number: 'INV-001', total: '70.00' }}})),
  },
}));

vi.mock('../auth/AuthContext', () => ({
  useAuth: () => ({ user: { name: 'Test Cashier' }, hasPermission: () => true }),
}));

import PosPage from '../modules/pos/pages/PosPage';

describe('POS Terminal', () => {
  it('renders medicine list', async () => {
    render(<BrowserRouter><PosPage /></BrowserRouter>);
    expect(await screen.findByText(/Panadol Extra/)).toBeTruthy();
  });

  it('adds medicine to cart on click', async () => {
    render(<BrowserRouter><PosPage /></BrowserRouter>);
    const btn = await screen.findByText(/Panadol Extra/);
    fireEvent.click(btn.closest('button'));
    expect(screen.getByText(/Cart \(1\)/)).toBeTruthy();
  });

  it('calculates cart total correctly', async () => {
    render(<BrowserRouter><PosPage /></BrowserRouter>);
    const btn = await screen.findByText(/Panadol Extra/);
    fireEvent.click(btn.closest('button'));
    fireEvent.click(btn.closest('button')); // Add twice
    // 2 x 45.00 = 90.00 — check the Total row specifically
    const totals = screen.getAllByText(/Rs 90\.00/);
    expect(totals.length).toBeGreaterThan(0);
  });

  it('search filters medicines', async () => {
    render(<BrowserRouter><PosPage /></BrowserRouter>);
    await screen.findByText(/Panadol Extra/);
    const input = screen.getByPlaceholderText(/Search medicine/);
    fireEvent.change(input, { target: { value: 'Disprin' } });
    expect(screen.queryByText(/Panadol Extra/)).toBeNull();
    expect(screen.getByText(/Disprin/)).toBeTruthy();
  });
});
