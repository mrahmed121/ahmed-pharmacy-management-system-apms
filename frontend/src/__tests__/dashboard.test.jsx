import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';

vi.mock('../api/client', () => ({
  default: { get: vi.fn(() => Promise.resolve({ data: { data: {
    today_revenue: '1500.00', today_invoices: 12, low_stock_count: 2, near_expiry_count: 1,
  }}})) },
}));

vi.mock('../auth/AuthContext', () => ({
  useAuth: () => ({ user: { name: 'Test Owner' }, hasPermission: () => true }),
}));

import Dashboard from '../pages/Dashboard';

describe('Dashboard', () => {
  it('renders welcome message', async () => {
    render(<BrowserRouter><Dashboard /></BrowserRouter>);
    expect(await screen.findByText(/Welcome, Test/)).toBeTruthy();
  });

  it('shows low stock alert when present', async () => {
    render(<BrowserRouter><Dashboard /></BrowserRouter>);
    expect(await screen.findByText(/2 medicines low on stock/)).toBeTruthy();
  });

  it('displays today revenue', async () => {
    render(<BrowserRouter><Dashboard /></BrowserRouter>);
    expect(await screen.findByText(/Rs 1500\.00/)).toBeTruthy();
  });
});
