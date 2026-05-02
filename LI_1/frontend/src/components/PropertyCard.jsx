import { Link } from 'react-router-dom';

export default function PropertyCard({ property }) {
  return (
    <Link to={`/properties/${property.id}`} className="block bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
      <div className="p-5">
        <div className="text-sm text-slate-500 mb-2">{property.property_type}</div>
        <h2 className="text-xl font-semibold mb-2">{property.name}</h2>
        <p className="text-slate-600 line-clamp-3">{property.description}</p>
        <div className="mt-4 flex items-center justify-between text-slate-700 font-medium">
          <span>{property.location}</span>
          <span>${property.price_per_day} / day</span>
        </div>
      </div>
    </Link>
  );
}
