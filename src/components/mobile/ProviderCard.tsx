import { Heart, Star, CheckCircle, MapPin } from "lucide-react";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";

interface ProviderCardProps {
  id: string;
  name: string;
  role: string;
  image: string;
  rating: number;
  reviewCount: number;
  hourlyRate: number;
  distance: string;
  verified: boolean;
  badges: string[];
  isFavorite?: boolean;
  onToggleFavorite?: (id: string) => void;
  onClick?: () => void;
}

export function ProviderCard({
  id,
  name,
  role,
  image,
  rating,
  reviewCount,
  hourlyRate,
  distance,
  verified,
  badges,
  isFavorite = false,
  onToggleFavorite,
  onClick,
}: ProviderCardProps) {
  return (
    <Card 
      variant="interactive"
      className="overflow-hidden"
      onClick={onClick}
    >
      <div className="relative">
        <img 
          src={image} 
          alt={name}
          className="w-full h-40 object-cover"
        />
        <div className="absolute top-3 right-3">
          <Button
            variant="ghost"
            size="icon-sm"
            className="h-9 w-9 rounded-full bg-card/80 backdrop-blur-sm"
            onClick={(e) => {
              e.stopPropagation();
              onToggleFavorite?.(id);
            }}
          >
            <Heart 
              className={cn(
                "h-5 w-5 transition-colors",
                isFavorite ? "fill-primary text-primary" : "text-foreground"
              )} 
            />
          </Button>
        </div>
        {verified && (
          <div className="absolute top-3 left-3">
            <Badge variant="verified" className="gap-1">
              <CheckCircle className="h-3 w-3" />
              DBS Verified
            </Badge>
          </div>
        )}
      </div>
      
      <div className="p-4">
        <div className="flex items-start justify-between mb-2">
          <div>
            <h3 className="font-semibold text-foreground">{name}</h3>
            <p className="text-sm text-muted-foreground">{role}</p>
          </div>
          <div className="text-right">
            <p className="font-semibold text-foreground">£{hourlyRate}<span className="text-sm font-normal text-muted-foreground">/hr</span></p>
          </div>
        </div>
        
        <div className="flex items-center gap-4 mb-3">
          <div className="flex items-center gap-1">
            <Star className="h-4 w-4 fill-warning text-warning" />
            <span className="text-sm font-medium">{rating.toFixed(1)}</span>
            <span className="text-sm text-muted-foreground">({reviewCount})</span>
          </div>
          <div className="flex items-center gap-1 text-muted-foreground">
            <MapPin className="h-4 w-4" />
            <span className="text-sm">{distance}</span>
          </div>
        </div>
        
        <div className="flex flex-wrap gap-2">
          {badges.slice(0, 3).map((badge) => (
            <Badge key={badge} variant="secondary" className="text-xs">
              {badge}
            </Badge>
          ))}
        </div>
      </div>
    </Card>
  );
}
