import { useState } from 'react';
import api from '../api/axios';
import ValidationMessage from './ValidationMessage';

export default function BookingForm({ property }) {
  const [form, setForm] = useState({ date_from: '', date_to: '', guests: 1, contact_phone: '', notes: '' });
  const [errors, setErrors] = useState({});
  const [success, setSuccess] = useState(null);

  const computeTotal = () => {
    if (!form.date_from || !form.date_to) return '0.00';
    const from = new Date(form.date_from);
    const to = new Date(form.date_to);
    const days = Math.max(0, Math.ceil((to - from) / (1000 * 60 * 60 * 24)));
    return (days * property.price_per_day * form.guests).toFixed(2);
  };

  const validate = () => {
    const fieldErrors = {};
    if (!form.date_from) fieldErrors.date_from = 'Start date is required';
    if (!form.date_to) fieldErrors.date_to = 'End date is required';
    if (form.date_from && form.date_to && form.date_from >= form.date_to) fieldErrors.date_to = 'End date must be after start date';
    if (!form.guests || form.guests < 1) fieldErrors.guests = 'At least 1 guest required';
    if (form.guests > property.capacity) fieldErrors.guests = `Max capacity is ${property.capacity}`;
    if (!form.contact_phone || form.contact_phone.length < 7) fieldErrors.contact_phone = 'Enter a valid phone number';
    if (form.notes && form.notes.length > 500) fieldErrors.notes = 'Notes must be under 500 characters';
    return fieldErrors;
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    const fieldErrors = validate();
    if (Object.keys(fieldErrors).length > 0) {
      setErrors(fieldErrors);
      return;
    }

    try {
      await api.post('/api/bookings', { ...form, property_id: property.id, total_price: parseFloat(computeTotal()) });
      setSuccess('Booking created successfully.');
      setErrors({});
    } catch (error) {
      setErrors({ form: 'Unable to create booking. Please try again.' });
    }
  };

  return (
    <div className="bg-white p-6 rounded-xl shadow-sm">
      <h2 className="text-xl font-semibold mb-4">Book this property</h2>
      {errors.form && <div className="text-red-600 mb-4">{errors.form}</div>}
      {success && <div className="text-green-600 mb-4">{success}</div>}
      <form className="space-y-4" onSubmit={handleSubmit}>
        <div className="grid gap-4 md:grid-cols-2">
          <label className="block">
            <span className="text-sm font-medium">From</span>
            <input type="date" value={form.date_from} onChange={(e) => setForm({ ...form, date_from: e.target.value })} className="mt-2 w-full" />
            <ValidationMessage message={errors.date_from} />
          </label>
          <label className="block">
            <span className="text-sm font-medium">To</span>
            <input type="date" value={form.date_to} onChange={(e) => setForm({ ...form, date_to: e.target.value })} className="mt-2 w-full" />
            <ValidationMessage message={errors.date_to} />
          </label>
        </div>
        <div className="grid gap-4 md:grid-cols-2">
          <label className="block">
            <span className="text-sm font-medium">Guests</span>
            <input type="number" min="1" value={form.guests} onChange={(e) => setForm({ ...form, guests: Number(e.target.value) })} className="mt-2 w-full" />
            <ValidationMessage message={errors.guests} />
          </label>
          <label className="block">
            <span className="text-sm font-medium">Phone</span>
            <input type="tel" value={form.contact_phone} onChange={(e) => setForm({ ...form, contact_phone: e.target.value })} className="mt-2 w-full" />
            <ValidationMessage message={errors.contact_phone} />
          </label>
        </div>
        <label className="block">
          <span className="text-sm font-medium">Notes</span>
          <textarea value={form.notes} onChange={(e) => setForm({ ...form, notes: e.target.value })} className="mt-2 w-full h-28" />
          <ValidationMessage message={errors.notes} />
        </label>
        <div className="grid gap-4 md:grid-cols-2">
          <div>
            <span className="text-sm font-medium">Property type</span>
            <div className="mt-2 p-3 rounded-md bg-slate-50">{property.property_type}</div>
          </div>
          <div>
            <span className="text-sm font-medium">Total price</span>
            <div className="mt-2 p-3 rounded-md bg-slate-50">${computeTotal()}</div>
          </div>
        </div>
        <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-md">Reserve Now</button>
      </form>
    </div>
  );
}
