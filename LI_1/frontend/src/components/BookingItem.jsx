export default function BookingItem({ booking }) {
  return (
    <div className="bg-white p-5 rounded-xl shadow-sm grid gap-2 md:grid-cols-2">
      <div>
        <h3 className="text-lg font-semibold">{booking.property_name}</h3>
        <p className="text-slate-600">{booking.location} • {booking.property_type}</p>
        <p className="mt-2 text-sm">{booking.date_from} → {booking.date_to}</p>
      </div>
      <div className="self-end text-right">
        <p className="font-medium">Guests: {booking.guests}</p>
        <p className="text-slate-500">Status: {booking.status}</p>
        <p className="mt-2 font-semibold">${booking.total_price}</p>
      </div>
    </div>
  );
}
