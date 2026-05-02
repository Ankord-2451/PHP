import { useEffect, useState } from 'react';
import api from '../api/axios';
import PropertyCard from '../components/PropertyCard';
import SearchBar from '../components/SearchBar';

export default function HomePage() {
  const [properties, setProperties] = useState([]);
  const [filters, setFilters] = useState({});
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchProperties(filters);
  }, [filters]);

  const fetchProperties = async (searchFilters) => {
    try {
      const response = await api.get('/api/properties', { params: searchFilters });
      setProperties(response.data.data || []);
    } catch (err) {
      setError('Unable to load properties.');
    }
  };

  return (
    <div className="space-y-6">
      <div className="bg-white p-6 rounded-xl shadow-sm">
        <h1 className="text-3xl font-semibold">BookEase</h1>
        <p className="mt-2 text-slate-600">Find rooms, halls, and venues with search filters and live booking support.</p>
      </div>
      <SearchBar onSearch={setFilters} />
      {error && <div className="text-red-600">{error}</div>}
      <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        {properties.map((property) => (
          <PropertyCard key={property.id} property={property} />
        ))}
      </div>
    </div>
  );
}
