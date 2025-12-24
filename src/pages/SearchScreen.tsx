import { useState } from "react";
import { ChevronLeft, MapPin, SlidersHorizontal, X } from "lucide-react";
import { MobileLayout } from "@/components/mobile/MobileLayout";
import { BottomNav } from "@/components/mobile/BottomNav";
import { SearchBar } from "@/components/mobile/SearchBar";
import { ProviderCard } from "@/components/mobile/ProviderCard";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
  Drawer,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerClose,
} from "@/components/ui/drawer";
import { Slider } from "@/components/ui/slider";
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import { useNavigate, useSearchParams } from "react-router-dom";

import heroImage from "@/assets/hero-carer.jpg";
import childminderImage from "@/assets/childminder.jpg";
import housekeeperImage from "@/assets/housekeeper.jpg";

const mockProviders = [
  {
    id: "1",
    name: "Sarah Johnson",
    role: "Professional Carer",
    image: heroImage,
    rating: 4.9,
    reviewCount: 127,
    hourlyRate: 18,
    distance: "1.2 mi",
    verified: true,
    badges: ["First Aid", "CQC Registered", "10+ yrs exp"],
  },
  {
    id: "2",
    name: "Emily Chen",
    role: "Childminder",
    image: childminderImage,
    rating: 4.8,
    reviewCount: 89,
    hourlyRate: 15,
    distance: "0.8 mi",
    verified: true,
    badges: ["Ofsted Registered", "CPR Trained"],
  },
  {
    id: "3",
    name: "Maria Garcia",
    role: "Housekeeper",
    image: housekeeperImage,
    rating: 4.7,
    reviewCount: 64,
    hourlyRate: 14,
    distance: "2.1 mi",
    verified: true,
    badges: ["Deep Cleaning", "Laundry"],
  },
  {
    id: "4",
    name: "James Wilson",
    role: "Professional Carer",
    image: heroImage,
    rating: 4.6,
    reviewCount: 52,
    hourlyRate: 16,
    distance: "3.0 mi",
    verified: true,
    badges: ["Dementia Care", "Night Care"],
  },
  {
    id: "5",
    name: "Lisa Thompson",
    role: "Childminder",
    image: childminderImage,
    rating: 4.9,
    reviewCount: 98,
    hourlyRate: 17,
    distance: "1.5 mi",
    verified: true,
    badges: ["Early Years", "Special Needs"],
  },
];

const serviceTypes = [
  { id: "carer", label: "Carers" },
  { id: "childminder", label: "Childminders" },
  { id: "housekeeper", label: "Housekeepers" },
];

export default function SearchScreen() {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const [activeTab, setActiveTab] = useState("search");
  const [searchQuery, setSearchQuery] = useState(searchParams.get("q") || "");
  const [filterOpen, setFilterOpen] = useState(false);
  const [favorites, setFavorites] = useState<string[]>([]);
  const [location, setLocation] = useState("London, UK");
  
  // Filter states
  const [priceRange, setPriceRange] = useState([10, 30]);
  const [selectedServices, setSelectedServices] = useState<string[]>([]);
  const [dbsChecked, setDbsChecked] = useState(false);

  const toggleFavorite = (id: string) => {
    setFavorites((prev) =>
      prev.includes(id) ? prev.filter((f) => f !== id) : [...prev, id]
    );
  };

  const toggleService = (id: string) => {
    setSelectedServices((prev) =>
      prev.includes(id) ? prev.filter((s) => s !== id) : [...prev, id]
    );
  };

  const clearFilters = () => {
    setPriceRange([10, 30]);
    setSelectedServices([]);
    setDbsChecked(false);
  };

  return (
    <MobileLayout>
      {/* Header */}
      <header className="sticky top-0 z-40 bg-background/95 backdrop-blur-sm border-b border-border safe-area-top">
        <div className="px-4 py-3">
          <div className="flex items-center gap-3 mb-3">
            <Button
              variant="ghost"
              size="icon-sm"
              onClick={() => navigate(-1)}
              className="shrink-0"
            >
              <ChevronLeft className="h-5 w-5" />
            </Button>
            <div className="flex-1">
              <SearchBar
                value={searchQuery}
                onChange={setSearchQuery}
                onFilterClick={() => setFilterOpen(true)}
                placeholder="Search providers..."
              />
            </div>
          </div>
          
          {/* Location */}
          <div className="flex items-center gap-2 text-sm">
            <MapPin className="h-4 w-4 text-primary" />
            <span className="text-muted-foreground">Near:</span>
            <Button variant="ghost" size="sm" className="h-auto p-0 text-foreground font-medium">
              {location}
            </Button>
          </div>
        </div>
      </header>

      <main className="pb-24 px-4">
        {/* Results Count */}
        <div className="py-4 flex items-center justify-between">
          <p className="text-sm text-muted-foreground">
            <span className="font-semibold text-foreground">{mockProviders.length}</span> providers found
          </p>
          <div className="flex gap-2">
            {selectedServices.length > 0 && (
              <Badge variant="secondary" className="gap-1">
                {selectedServices.length} filters
                <X className="h-3 w-3 cursor-pointer" onClick={clearFilters} />
              </Badge>
            )}
          </div>
        </div>

        {/* Provider List */}
        <div className="space-y-4">
          {mockProviders.map((provider, index) => (
            <div 
              key={provider.id}
              className="animate-slide-up"
              style={{ animationDelay: `${index * 0.05}s` }}
            >
              <ProviderCard
                {...provider}
                isFavorite={favorites.includes(provider.id)}
                onToggleFavorite={toggleFavorite}
                onClick={() => navigate(`/provider/${provider.id}`)}
              />
            </div>
          ))}
        </div>
      </main>

      {/* Filter Drawer */}
      <Drawer open={filterOpen} onOpenChange={setFilterOpen}>
        <DrawerContent>
          <DrawerHeader className="border-b border-border">
            <div className="flex items-center justify-between">
              <DrawerTitle>Filters</DrawerTitle>
              <Button variant="ghost" size="sm" onClick={clearFilters}>
                Clear all
              </Button>
            </div>
          </DrawerHeader>
          
          <div className="p-4 space-y-6">
            {/* Service Type */}
            <div>
              <h4 className="font-medium text-foreground mb-3">Service Type</h4>
              <div className="flex flex-wrap gap-2">
                {serviceTypes.map((service) => (
                  <Badge
                    key={service.id}
                    variant={selectedServices.includes(service.id) ? "default" : "outline"}
                    className="cursor-pointer"
                    onClick={() => toggleService(service.id)}
                  >
                    {service.label}
                  </Badge>
                ))}
              </div>
            </div>

            {/* Price Range */}
            <div>
              <h4 className="font-medium text-foreground mb-3">
                Hourly Rate: £{priceRange[0]} - £{priceRange[1]}
              </h4>
              <Slider
                value={priceRange}
                onValueChange={setPriceRange}
                min={5}
                max={50}
                step={1}
                className="mt-2"
              />
            </div>

            {/* DBS Check */}
            <div className="flex items-center space-x-3">
              <Checkbox
                id="dbs"
                checked={dbsChecked}
                onCheckedChange={(checked) => setDbsChecked(checked as boolean)}
              />
              <Label htmlFor="dbs" className="text-sm font-medium">
                Enhanced DBS Checked Only
              </Label>
            </div>

            {/* Apply Button */}
            <Button className="w-full" onClick={() => setFilterOpen(false)}>
              Apply Filters
            </Button>
          </div>
        </DrawerContent>
      </Drawer>

      <BottomNav activeTab={activeTab} onTabChange={setActiveTab} />
    </MobileLayout>
  );
}
