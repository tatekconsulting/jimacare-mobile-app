import { useState } from "react";
import { Heart, Baby, Home as HomeIcon, ChevronRight, Shield, Clock, Users } from "lucide-react";
import { MobileLayout } from "@/components/mobile/MobileLayout";
import { BottomNav } from "@/components/mobile/BottomNav";
import { Header } from "@/components/mobile/Header";
import { SearchBar } from "@/components/mobile/SearchBar";
import { ServiceCard } from "@/components/mobile/ServiceCard";
import { ProviderCard } from "@/components/mobile/ProviderCard";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";

import heroImage from "@/assets/hero-carer.jpg";
import childminderImage from "@/assets/childminder.jpg";
import housekeeperImage from "@/assets/housekeeper.jpg";

const services = [
  {
    id: "carer",
    title: "Carers",
    description: "Elderly & home care",
    icon: Heart,
    image: heroImage,
  },
  {
    id: "childminder",
    title: "Childminders",
    description: "Trusted childcare",
    icon: Baby,
    image: childminderImage,
  },
  {
    id: "housekeeper",
    title: "Housekeepers",
    description: "Home management",
    icon: HomeIcon,
    image: housekeeperImage,
  },
];

const featuredProviders = [
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
    badges: ["Ofsted Registered", "CPR Trained", "Pet Friendly"],
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
    badges: ["Deep Cleaning", "Laundry", "Cooking"],
  },
];

const trustBadges = [
  { icon: Shield, label: "DBS Checked" },
  { icon: Clock, label: "24/7 Support" },
  { icon: Users, label: "10K+ Carers" },
];

export default function HomeScreen() {
  const [activeTab, setActiveTab] = useState("home");
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedService, setSelectedService] = useState<string | null>(null);
  const [favorites, setFavorites] = useState<string[]>([]);

  const toggleFavorite = (id: string) => {
    setFavorites((prev) =>
      prev.includes(id) ? prev.filter((f) => f !== id) : [...prev, id]
    );
  };

  return (
    <MobileLayout>
      <Header />
      
      <main className="pb-24 px-4">
        {/* Hero Section */}
        <section className="py-6">
          <h1 className="text-2xl font-bold text-foreground mb-1">
            Find Trusted Care
          </h1>
          <p className="text-muted-foreground mb-4">
            Verified professionals near you
          </p>
          
          {/* Trust Badges */}
          <div className="flex gap-2 mb-6 overflow-x-auto pb-2 -mx-4 px-4">
            {trustBadges.map((badge) => (
              <Badge 
                key={badge.label}
                variant="verified" 
                className="shrink-0 gap-1.5 py-1.5 px-3"
              >
                <badge.icon className="h-3.5 w-3.5" />
                {badge.label}
              </Badge>
            ))}
          </div>
          
          <SearchBar
            value={searchQuery}
            onChange={setSearchQuery}
            placeholder="Search by name, service, location..."
          />
        </section>

        {/* Services Section */}
        <section className="mb-8">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-foreground">Our Services</h2>
            <Button variant="ghost" size="sm" className="text-primary gap-1">
              View all
              <ChevronRight className="h-4 w-4" />
            </Button>
          </div>
          
          <div className="grid grid-cols-2 gap-3">
            {services.slice(0, 2).map((service) => (
              <ServiceCard
                key={service.id}
                {...service}
                isSelected={selectedService === service.id}
                onClick={() => setSelectedService(
                  selectedService === service.id ? null : service.id
                )}
              />
            ))}
          </div>
          <div className="mt-3">
            <ServiceCard
              {...services[2]}
              isSelected={selectedService === services[2].id}
              onClick={() => setSelectedService(
                selectedService === services[2].id ? null : services[2].id
              )}
            />
          </div>
        </section>

        {/* How It Works */}
        <section className="mb-8">
          <Card variant="flat" className="bg-accent p-4 rounded-2xl">
            <h3 className="font-semibold text-foreground mb-3">How JimaCare Works</h3>
            <div className="space-y-3">
              {[
                { step: 1, text: "Search for care professionals" },
                { step: 2, text: "Review profiles & ratings" },
                { step: 3, text: "Book & connect directly" },
              ].map((item) => (
                <div key={item.step} className="flex items-center gap-3">
                  <div className="w-8 h-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-semibold">
                    {item.step}
                  </div>
                  <span className="text-sm text-foreground">{item.text}</span>
                </div>
              ))}
            </div>
          </Card>
        </section>

        {/* Featured Providers */}
        <section>
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-foreground">Top Rated Near You</h2>
            <Button variant="ghost" size="sm" className="text-primary gap-1">
              See all
              <ChevronRight className="h-4 w-4" />
            </Button>
          </div>
          
          <div className="space-y-4">
            {featuredProviders.map((provider, index) => (
              <div 
                key={provider.id}
                className="animate-slide-up"
                style={{ animationDelay: `${index * 0.1}s` }}
              >
                <ProviderCard
                  {...provider}
                  isFavorite={favorites.includes(provider.id)}
                  onToggleFavorite={toggleFavorite}
                />
              </div>
            ))}
          </div>
        </section>
      </main>

      <BottomNav activeTab={activeTab} onTabChange={setActiveTab} />
    </MobileLayout>
  );
}
