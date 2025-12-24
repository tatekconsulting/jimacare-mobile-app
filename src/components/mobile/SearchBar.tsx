import { Search, SlidersHorizontal } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

interface SearchBarProps {
  value: string;
  onChange: (value: string) => void;
  onFilterClick?: () => void;
  placeholder?: string;
}

export function SearchBar({ 
  value, 
  onChange, 
  onFilterClick,
  placeholder = "Search carers, childminders..." 
}: SearchBarProps) {
  return (
    <div className="flex items-center gap-2">
      <div className="relative flex-1">
        <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
        <Input
          value={value}
          onChange={(e) => onChange(e.target.value)}
          placeholder={placeholder}
          className="pl-12 h-14 rounded-2xl bg-card border-border"
        />
      </div>
      <Button 
        variant="secondary" 
        size="icon-lg"
        className="rounded-2xl shrink-0"
        onClick={onFilterClick}
      >
        <SlidersHorizontal className="h-5 w-5" />
      </Button>
    </div>
  );
}
