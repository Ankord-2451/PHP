import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../api/axios';
import BookingForm from '../components/BookingForm';

export default function PropertyPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [property, setProperty] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (id) {
      api.get(`/api/properties/${id}`)
        .then((response) => setProperty(response.data.data))
        .catch(() => setError('Could not load property.'));
    }
  }, [id]);

  if (error) {
    return <div className="text-red-600">{error}</div>;
  }

  if (!property) {
    return <div>Loading property details…</div>;
  }

  return (
    <div className="space-y-6">
      <button className="text-blue-600" onClick={() => navigate(-1)}>&larr; Back</button>
      <div className="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
          <div>
            <h1 className="text-2xl font-semibold">{property.name}</h1>
            <p className="mt-2 text-slate-600">{property.description}</p>
          </div>
          <div className="text-right">
            <div className="text-slate-500">Type</div>
            <div className="text-lg font-semibold">{property.property_type}</div>
          </div>
        </div>
        <div className="grid gap-4 md:grid-cols-3">
          <div className="p-4 bg-slate-50 rounded-lg">Location: {property.location}</div>
          <div className="p-4 bg-slate-50 rounded-lg">Capacity: {property.capacity}</div>
          <div className="p-4 bg-slate-50 rounded-lg">Views: {property.views ?? 0}</div>
        </div>
      </div>
      <BookingForm property={property} />
    </div>
  );
}
