import { useContext, useEffect, useState } from 'react';
import { AuthContext } from '../context/AuthContext';
import api from '../api/axios';
import AdminUserTable from '../components/AdminUserTable';
import AdminBookingTable from '../components/AdminBookingTable';
import AdminPropertyForm from '../components/AdminPropertyForm';

export default function AdminPage() {
  const auth = useContext(AuthContext);
  const [users, setUsers] = useState([]);
  const [bookings, setBookings] = useState([]);
  const [stats, setStats] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => {
    Promise.all([
      api.get('/api/admin/users'),
      api.get('/api/admin/bookings'),
      api.get('/api/admin/stats'),
    ])
      .then(([usersRes, bookingsRes, statsRes]) => {
        setUsers(usersRes.data.data || []);
        setBookings(bookingsRes.data.data || []);
        setStats(statsRes.data.data);
      })
      .catch(() => setError('Unable to load admin data.'));
  }, []);

  return (
    <div className="space-y-6">
      <div className="bg-white p-6 rounded-xl shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-semibold">Admin Panel</h1>
          <p className="text-slate-600">Manage users, bookings, and property catalog.</p>
        </div>
        <div className="text-slate-500">Signed in as: {auth.user?.email}</div>
      </div>
      {error && <div className="text-red-600">{error}</div>}
      {stats && (
        <div className="grid gap-4 md:grid-cols-3">
          <div className="bg-white p-4 rounded-xl shadow-sm">Total users: {stats.total_users}</div>
          <div className="bg-white p-4 rounded-xl shadow-sm">Total properties: {stats.total_properties}</div>
          <div className="bg-white p-4 rounded-xl shadow-sm">Total bookings: {stats.total_bookings}</div>
        </div>
      )}
      <AdminUserTable users={users} />
      <AdminPropertyForm />
      <AdminBookingTable bookings={bookings} />
    </div>
  );
}
