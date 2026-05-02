import { useContext, useEffect, useState } from 'react';
import api from '../api/axios';
import { AuthContext } from '../context/AuthContext';
import BookingItem from '../components/BookingItem';

export default function BookingsPage() {
  const auth = useContext(AuthContext);
  const [bookings, setBookings] = useState([]);
  const [error, setError] = useState(null);

  useEffect(() => {
    api.get('/api/bookings')
      .then((response) => setBookings(response.data.data || []))
      .catch(() => setError('Unable to load your bookings.'));
  }, []);

  return (
    <div className="space-y-6">
      <div className="bg-white p-6 rounded-xl shadow-sm">
        <div className="flex items-center justify-between">
          <h1 className="text-2xl font-semibold">My Bookings</h1>
          <div className="text-slate-500">{auth.user?.name}</div>
        </div>
      </div>
      {error && <div className="text-red-600">{error}</div>}
      <div className="space-y-4">
        {bookings.length === 0 ? (
          <div className="bg-white p-6 rounded-xl shadow-sm">No bookings found.</div>
        ) : (
          bookings.map((booking) => <BookingItem key={booking.id} booking={booking} />)
        )}
      </div>
    </div>
  );
}
