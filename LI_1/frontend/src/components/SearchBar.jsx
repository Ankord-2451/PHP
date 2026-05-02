import { useState } from 'react';

export default function SearchBar({ onSearch }) {
  const [filters, setFilters] = useState({ location: '', property_type: '', max_price: '', min_capacity: '' });

  const handleSubmit = (event) => {
    event.preventDefault();
    onSearch(filters);
  };

  return (
    <form onSubmit={handleSubmit} className="bg-white p-6 rounded-xl shadow-sm grid gap-4 md:grid-cols-5">
      <div>
        <label className="block text-sm font-medium">Location</label>
        <input value={filters.location} onChange={(e) => setFilters({ ...filters, location: e.target.value })} className="mt-2 w-full" />
      </div>
      <div>
        <label className="block text-sm font-medium">Type</label>
        <select value={filters.property_type} onChange={(e) => setFilters({ ...filters, property_type: e.target.value })} className="mt-2 w-full">
          <option value="">Any</option>
          <option value="room">Room</option>
          <option value="hall">Hall</option>
          <option value="venue">Venue</option>
          <option value="apartment">Apartment</option>
        </select>
      </div>
      <div>
        <label className="block text-sm font-medium">Max price</label>
        <input type="number" min="0" value={filters.max_price} onChange={(e) => setFilters({ ...filters, max_price: e.target.value })} className="mt-2 w-full" />
      </div>
      <div>
        <label className="block text-sm font-medium">Min capacity</label>
        <input type="number" min="1" value={filters.min_capacity} onChange={(e) => setFilters({ ...filters, min_capacity: e.target.value })} className="mt-2 w-full" />
      </div>
      <div className="flex items-end">
        <button type="submit" className="w-full bg-blue-600 text-white py-2 rounded-md">Search</button>
      </div>
    </form>
  );
}
