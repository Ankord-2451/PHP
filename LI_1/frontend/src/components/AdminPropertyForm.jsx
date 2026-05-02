import { useState } from 'react';
import api from '../api/axios';
import ValidationMessage from './ValidationMessage';

export default function AdminPropertyForm() {
  const [form, setForm] = useState({ name: '', property_type: 'room', location: '', price_per_day: 0, capacity: 1, description: '', amenities: '', is_active: true });
  const [errors, setErrors] = useState({});
  const [success, setSuccess] = useState(null);

  const validate = () => {
    const fieldErrors = {};
    if (!form.name || form.name.length < 3) fieldErrors.name = 'Name must be at least 3 characters.';
    if (!form.location || form.location.length < 2) fieldErrors.location = 'Location is required.';
    if (!form.price_per_day || form.price_per_day <= 0) fieldErrors.price_per_day = 'Price must be positive.';
    if (!form.capacity || form.capacity < 1) fieldErrors.capacity = 'Capacity must be at least 1.';
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
      await api.post('/api/properties', form);
      setSuccess('Property created successfully.');
      setErrors({});
      setForm({ name: '', property_type: 'room', location: '', price_per_day: 0, capacity: 1, description: '', amenities: '', is_active: true });
    } catch {
      setErrors({ form: 'Unable to create property. Please check your inputs.' });
    }
  };

  return (
    <div className="bg-white p-6 rounded-xl shadow-sm">
      <h2 className="text-xl font-semibold mb-4">Create Property</h2>
      {errors.form && <div className="text-red-600 mb-4">{errors.form}</div>}
      {success && <div className="text-green-600 mb-4">{success}</div>}
      <form className="grid gap-4" onSubmit={handleSubmit}>
        <div>
          <label className="block text-sm font-medium">Name</label>
          <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className="mt-2 w-full" />
          <ValidationMessage message={errors.name} />
        </div>
        <div className="grid gap-4 md:grid-cols-2">
          <label className="block">
            <span className="text-sm font-medium">Type</span>
            <select value={form.property_type} onChange={(e) => setForm({ ...form, property_type: e.target.value })} className="mt-2 w-full">
              <option value="room">Room</option>
              <option value="hall">Hall</option>
              <option value="venue">Venue</option>
              <option value="apartment">Apartment</option>
            </select>
          </label>
          <label className="block">
            <span className="text-sm font-medium">Location</span>
            <input value={form.location} onChange={(e) => setForm({ ...form, location: e.target.value })} className="mt-2 w-full" />
            <ValidationMessage message={errors.location} />
          </label>
        </div>
        <div className="grid gap-4 md:grid-cols-2">
          <label className="block">
            <span className="text-sm font-medium">Price per day</span>
            <input type="number" min="0.01" step="0.01" value={form.price_per_day} onChange={(e) => setForm({ ...form, price_per_day: parseFloat(e.target.value) })} className="mt-2 w-full" />
            <ValidationMessage message={errors.price_per_day} />
          </label>
          <label className="block">
            <span className="text-sm font-medium">Capacity</span>
            <input type="number" min="1" value={form.capacity} onChange={(e) => setForm({ ...form, capacity: Number(e.target.value) })} className="mt-2 w-full" />
            <ValidationMessage message={errors.capacity} />
          </label>
        </div>
        <label className="block">
          <span className="text-sm font-medium">Description</span>
          <textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} className="mt-2 w-full h-24" />
        </label>
        <label className="block">
          <span className="text-sm font-medium">Amenities</span>
          <input value={form.amenities} onChange={(e) => setForm({ ...form, amenities: e.target.value })} className="mt-2 w-full" />
        </label>
        <label className="flex items-center gap-2">
          <input type="checkbox" checked={form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} />
          <span className="text-sm">Active</span>
        </label>
        <button type="submit" className="w-full bg-green-600 text-white py-2 rounded-md">Create Property</button>
      </form>
    </div>
  );
}
