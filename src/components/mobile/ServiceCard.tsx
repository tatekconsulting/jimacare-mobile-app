import { cn } from "@/lib/utils";
import { LucideIcon } from "lucide-react";

interface ServiceCardProps {
  title: string;
  description: string;
  icon: LucideIcon;
  image: string;
  isSelected?: boolean;
  onClick?: () => void;
}

export function ServiceCard({
  title,
  description,
  icon: Icon,
  image,
  isSelected = false,
  onClick,
}: ServiceCardProps) {
  return (
    <button
      onClick={onClick}
      className={cn(
        "relative overflow-hidden rounded-2xl w-full aspect-[4/3] group transition-all duration-300",
        isSelected && "ring-2 ring-primary ring-offset-2"
      )}
    >
      <img 
        src={image} 
        alt={title}
        className="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
      />
      <div className="absolute inset-0 bg-gradient-to-t from-foreground/80 via-foreground/20 to-transparent" />
      
      <div className="absolute inset-0 p-4 flex flex-col justify-end text-left">
        <div className={cn(
          "w-10 h-10 rounded-xl flex items-center justify-center mb-2 transition-colors",
          isSelected ? "bg-primary" : "bg-card/90"
        )}>
          <Icon className={cn(
            "h-5 w-5",
            isSelected ? "text-primary-foreground" : "text-primary"
          )} />
        </div>
        <h3 className="font-semibold text-lg text-card">{title}</h3>
        <p className="text-sm text-card/80">{description}</p>
      </div>
    </button>
  );
}
