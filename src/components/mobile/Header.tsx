import { Bell, MapPin } from "lucide-react";
import { Button } from "@/components/ui/button";

interface HeaderProps {
  location?: string;
}

export function Header({ location = "London, UK" }: HeaderProps) {
  return (
    <header className="sticky top-0 z-40 glass-effect safe-area-top">
      <div className="flex items-center justify-between px-4 py-3">
        <div className="flex items-center gap-2">
          <div className="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
            <span className="text-primary-foreground font-bold text-lg">J</span>
          </div>
          <div>
            <p className="text-xs text-muted-foreground">Your location</p>
            <button className="flex items-center gap-1 text-sm font-medium text-foreground">
              <MapPin className="h-3.5 w-3.5 text-primary" />
              {location}
            </button>
          </div>
        </div>
        
        <Button variant="ghost" size="icon-sm" className="relative">
          <Bell className="h-5 w-5" />
          <span className="absolute top-1 right-1 h-2 w-2 rounded-full bg-primary" />
        </Button>
      </div>
    </header>
  );
}
