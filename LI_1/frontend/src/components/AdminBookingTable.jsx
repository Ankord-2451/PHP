import api from '../api/axios';

export default function AdminBookingTable({ bookings }) {
  const updateStatus = async (id, status) => {
    try {
      await api.put(`/api/bookings/${id}`, { status });
      window.location.reload();
    } catch {
      alert('Unable to update booking status.');
    }
  };

  return (
    <div className="bg-white p-6 rounded-xl shadow-sm">
      <h2 className="text-xl font-semibold mb-4">Booking Oversight</h2>
      <div className="overflow-x-auto">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b border-slate-200">
              <th className="py-3">User</th>
              <th className="py-3">Property</th>
              <th className="py-3">Dates</th>
              <th className="py-3">Guests</th>
              <th className="py-3">Status</th>
              <th className="py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {bookings.map((booking) => (
              <tr key={booking.id} className="border-b border-slate-100">
                <td className="py-3">{booking.user_name}</td>
                <td className="py-3">{booking.property_name}</td>
                <td className="py-3">{booking.date_from} → {booking.date_to}</td>
                <td className="py-3">{booking.guests}</td>
                <td className="py-3">{booking.status}</td>
                <td className="py-3 space-x-2">
                  <button onClick={() => updateStatus(booking.id, 'confirmed')} className="text-green-600">Confirm</button>
                  <button onClick={() => updateStatus(booking.id, 'cancelled')} className="text-red-600">Cancel</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
