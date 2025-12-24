import { useState } from "react";
import { Heart } from "lucide-react";
import { MobileLayout } from "@/components/mobile/MobileLayout";
import { BottomNav } from "@/components/mobile/BottomNav";
import { ProviderCard } from "@/components/mobile/ProviderCard";
import { Button } from "@/components/ui/button";
import { useNavigate } from "react-router-dom";

import heroImage from "@/assets/hero-carer.jpg";
import childminderImage from "@/assets/childminder.jpg";

const mockFavorites = [
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
    badges: ["First Aid", "CQC Registered"],
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
];

export default function FavoritesScreen() {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState("favorites");
  const [favorites, setFavorites] = useState<string[]>(mockFavorites.map((p) => p.id));

  const toggleFavorite = (id: string) => {
    setFavorites((prev) =>
      prev.includes(id) ? prev.filter((f) => f !== id) : [...prev, id]
    );
  };

  const favoriteProviders = mockFavorites.filter((p) => favorites.includes(p.id));

  return (
    <MobileLayout>
      {/* Header */}
      <header className="sticky top-0 z-40 bg-background/95 backdrop-blur-sm border-b border-border safe-area-top">
        <div className="px-4 py-4">
          <h1 className="text-xl font-bold text-foreground">My Favorites</h1>
          <p className="text-sm text-muted-foreground">
            {favoriteProviders.length} saved providers
          </p>
        </div>
      </header>

      <main className="pb-24 px-4">
        {favoriteProviders.length > 0 ? (
          <div className="space-y-4 py-4">
            {favoriteProviders.map((provider, index) => (
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
        ) : (
          <div className="flex flex-col items-center justify-center py-16 text-center">
            <div className="w-20 h-20 rounded-full bg-accent flex items-center justify-center mb-4">
              <Heart className="h-10 w-10 text-primary" />
            </div>
            <h2 className="text-lg font-semibold text-foreground mb-2">
              No favorites yet
            </h2>
            <p className="text-sm text-muted-foreground mb-6 max-w-xs">
              Start saving your favorite care providers to quickly access them later
            </p>
            <Button onClick={() => navigate("/search")}>
              Browse Providers
            </Button>
          </div>
        )}
      </main>

      <BottomNav activeTab={activeTab} onTabChange={setActiveTab} />
    </MobileLayout>
  );
}
